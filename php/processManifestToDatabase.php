<?php
include 'manifestFunctions.php';
include 'YOUR-PATH-HERE/inc/api.inc';
include 'YOUR-PATH-HERE/inc/db.inc';
include 'YOUR-PATH-HERE/inc/server.inc';
include 'functions.php';

	//Retrieve the current manifest file name
	$url = $root. 'Destiny/Manifest/';
	echo $url. '<br/>'; //Troubleshooting
	
	$json = execute_curl($url,$apiKey);

	//Retrieve the remote path
	$path = $json['Response']['mobileWorldContentPaths']['en'];

	echo 'Remote path = ' .$path. '<br/>'; //Troubleshooting
	
	//Convert remote path to local path
	$localPath = pathinfo($path, PATHINFO_BASENAME);
	echo 'Local path = ' .$dataDir.$localPath. '<br/>';
	
	//Download the manifest if the file doesn't exist locally
	if (!file_exists($dataDir.$localPath)) {
		get_manifest($path, $dataDir.$localPath);
	}
	
	//Open Postgres database connection
	$pgConn = open_db_connect($dbhost, $dbport, $dbi, $dbuser, $dbpwd);
	
	//Check if the manifest should be processed into Postgres
	$sql = 'SELECT ' .$dbman. '.fn_process_manifest(\'' .$localPath. '\')';
	//echo $sql. '</br>'; //Troubleshooting
	
	$pgQuery = pg_query($pgConn, $sql);
	$result = pg_fetch_all($pgQuery);

	$process = json_decode($result[0]['fn_process_manifest'], true);

	if ($process['process'] == 'yes') {
	//if ('yes' == 'yes') {
	
		$manifest_id = $process['manifest_id'];
		//echo $manifest_id. '</br>';
	
		//Process the manifest
		echo 'Processing ' .$localPath. '...</br>';
		
		//Open the connection to the manifest*/
		$manConn = open_manifest_connection($dataDir.$localPath);
		
		//Process the metadata
		//Stage the manifest tables
		$tables = get_manifest_tables($manConn);
		$stgCount = 0;
		
		foreach ($tables as $table) {
			$count = get_manifest_table_record_count($manConn, $table);
			
			$sql = 'INSERT INTO ' .$dbman. '.t_stg_manifest_tables (manifest_id, table_name, record_count)';
			$sql .= ' VALUES (' .$manifest_id. ', \'' .$table. '\',' .$count. ')';
			
			$stgResult = pg_query($pgConn, $sql);
			
			$stgCount += pg_affected_rows($stgResult);
		}
		
		echo 'Staged ' .$stgCount. ' table(s)...</br>';
		
		//Identify new tables
		$sql = 'SELECT table_name, record_count FROM ' .$dbman. '.t_stg_manifest_tables';
		$sql .= ' WHERE NOT EXISTS (SELECT \'x\' FROM ' .$dbman. '.t_manifest_tables';
		$sql .= ' WHERE t_stg_manifest_tables.table_name = t_manifest_tables.table_name)';
		$sql .= ' ORDER BY table_name';
		
		//echo $sql. '</br>';
		$newResult = pg_query($pgConn, $sql);
		$newTables = pg_fetch_all($newResult);
		
		$newTableCount = 0;
		
		foreach ($newTables as $table) {
			$layout = get_manifest_table_layout($manConn, $table['table_name']);
			
			$sql = 'INSERT INTO ' .$dbman. '.t_manifest_tables (manifest_id, table_name, record_count, normal_layout)';
			$sql .= ' VALUES (\'' .$manifest_id. '\',\'' .$table['table_name']. '\',' .$table['record_count']. ', \'' .$layout. '\') RETURNING table_id';
			//echo $sql. '</br>';
			
			$newResult = pg_query($pgConn, $sql);
			$newTableCount += pg_affected_rows($newResult);
			
			$newTable = pg_fetch_all($newResult);
			$newTableId = $newTable[0]['table_id'];
			
			//Create the new table to hold the data
			echo 'Converting table name ' .$table['table_name']. '</br>';
			$sql = 'SELECT ' .$dbman. '.fn_convert_table_name(\'' .$table['table_name']. '\')';
			//echo $sql. '</br>';
			$nameResult = pg_query($pgConn, $sql);
			
			$nameData = pg_fetch_all($nameResult);
			$newTableName = $nameData[0]['fn_convert_table_name'];
			echo 'New table name is ' .$newTableName. '</br>';
			
			//Create the new table
			$sql = 'CREATE TABLE ' .$dbman. '.' .$newTableName. '(';
			
			if ($layout) {
				$sql .= ' id BIGINT, json jsonb)';
			} else {
				$sql .= ' key VARCHAR(100), json jsonb)';
			}
			
			pg_query($pgConn, $sql);
			
			//Add the table mapping
			$sql = 'INSERT INTO ' .$dbman. '.t_table_mappings (table_id, target_name) VALUES (';
			$sql .= $newTableId. ',\'' .$newTableName. '\')';
			
			pg_query($pgConn, $sql);
			
			//Add the field mappings
			$sql = 'INSERT INTO ' .$dbman. '.t_field_mappings (table_id, source_field, target_field, staging) VALUES (';
			
			if ($layout) {
				$sql .= $newTableId. ',\'id\', \'id\', true)';
			} else {
				$sql .= $newTableId. ',\'key\', \'key\', true)';
			}
			pg_query($pgConn, $sql);
			
			$sql = 'INSERT INTO ' .$dbman. '.t_field_mappings (table_id, source_field, target_field, staging) VALUES (';
			$sql .= $newTableId. ',\'json::jsonb\', \'json\', true)';
			pg_query($pgConn, $sql);
			
			$layout = true;
		}

		echo 'Loaded ' .$newTableCount. ' new table(s)...</br>';

		//Process the table data
		//Retrieve the tables to be processed
		$sql = 'SELECT DISTINCT table_name
			FROM ' .$dbman. '.t_stg_manifest_tables tsmt
			-- WHERE table_name != \'DestinyHistoricalStatsDefinition\'
			ORDER BY tsmt.table_name';
		
		$tableResult = pg_query($pgConn, $sql);
		$tables = pg_fetch_all($tableResult);
		
		foreach ($tables as $table) {
			$tableName = $table['table_name'];
			echo '</br>Table ' .$tableName. '</br>';
			//Retrieve target table
			$sql = 'SELECT tmt.table_name, tmt.record_count, tmt.normal_layout::int normal_layout, ttm.target_name
				FROM
					-- manifest.t_manifest_version tmv,
					' .$dbman. '.t_manifest_tables tmt, ' .$dbman. '.t_table_mappings ttm
				-- WHERE tmv.version = \'world_sql_content_b8ee8e3cc4c38460966cee2f10e238a3.content\'
				WHERE
					-- tmv.current = true AND
					tmt.table_name = \'' .$tableName. '\'
				-- AND tmv.manifest_id = tmt.manifest_id
				AND tmt.table_id = ttm.table_id
				ORDER BY tmt.table_name';
			
			//echo $sql. '</br>';
			
			$targetResult = pg_query($pgConn, $sql);
			$targetTable = pg_fetch_all($targetResult);
			
			$insertClause = 'INSERT INTO ' .$dbman. '.' .$targetTable[0]['target_name'];
			//echo $insertClause. '</br>';
			$expectedCount = $targetTable[0]['record_count'];
			$layout = (bool)$targetTable[0]['normal_layout'];
			//echo 'Layout ' .$layout. '</br>'; //Troubleshooting

			$sql = 'SELECT tmt.table_name, tfm.source_field, tfm.target_field
				FROM
					-- manifest.t_manifest_version tmv,
					' .$dbman. '.t_manifest_tables tmt, ' .$dbman. '.t_field_mappings tfm
				-- WHERE tmv.version = \'world_sql_content_b8ee8e3cc4c38460966cee2f10e238a3.content\'
				WHERE
					-- tmv.current = true AND
				tfm.staging = true
				AND tmt.table_name = \'' .$tableName. '\'
				-- AND tmv.manifest_id = tmt.manifest_id
				AND tmt.table_id = tfm.table_id
				ORDER BY tmt.table_name';
			
			//echo $sql. '</br>'; //Troubleshooting
			
			$fieldResult = pg_query($pgConn, $sql);
			$fields = pg_fetch_all($fieldResult);
			
			$sourceFields = null;
			$targetFields = null;
			
			foreach ($fields as $field) {
			
				if (!is_null($sourceFields)) {
					$sourceFields .= ', ';
					$targetFields .= ', ';
				}

				$sourceFields .= $field['source_field'];
				$targetFields .= $field['target_field'];
			}
			//echo $sourceFields. '</br>';
			//echo $targetFields. '</br>';
			
			if ($layout) {
				$stageTable = 't_stg_int_key';
				//echo $tableName. ' - ' .$stageTable. '</br>';
			} else {
				$stageTable = 't_stg_char_key';
				//echo $tableName. ' - ' .$stageTable. '</br>';
			}
			
			//Truncate the staging table
			$sql = 'TRUNCATE TABLE ' .$dbman. '.' .$stageTable;
			pg_query($pgConn, $sql);
			echo 'Truncated manifest.' .$stageTable. '</br>';
			
			//Retrieve data from manifest table
			$tableData = get_manifest_table_data($manConn, $tableName);
			$insertCount = 0;
			while ($row = $tableData->fetchArray()) {
				$insertSQL = 'INSERT INTO ' .$dbman. '.' .$stageTable. ' (' .$targetFields. ') VALUES (';
				
				if ($layout) {
					$insertSQL .= $row['id']. ',\'' .str_replace('\'', '\'\'', $row['json']). '\')';
				} else {
					$insertSQL .= '\'' .$row['key']. '\',\'' .str_replace('\'', '\'\'', $row['json']). '\')';
				}
				//echo $insertSQL. '</br>'; //Troubleshooting
				
				$insertResult = pg_query($pgConn, $insertSQL);
				//echo pg_errormessage($pgConn). '</br>';
				$insertCount += pg_affected_rows($insertResult);
			}

			//echo $tableName. ' ' .$insertCount. ' inserts...</br>'; //Troubleshooting
			
			//Push the staged data to the final table
			$sql = 'INSERT INTO ' .$dbman. '.' .$targetTable[0]['target_name']. ' (' .$targetFields. ') ';
			$sql .= '(SELECT ' .$sourceFields. ' FROM manifest.' .$stageTable. ')';
			
			//echo $sql. '</br>';
			
			$insertResult = pg_query($pgConn, $sql);
			$insertCount = pg_affected_rows($insertResult);
			
			echo 'Inserted ' .$insertCount. ' record(s)...</br>';
		}
		
	} else {
		echo $localPath. ' was processed previously...</br>';
	}
	
	//Close Postgres data connection
	pg_close($pgConn);
?>
