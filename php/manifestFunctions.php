<?php
//include '../inc/db.inc';

	function get_manifest($remotePath, $localPath) {
		echo 'Downloading https://www.bungie.net' .$remotePath.'<br/>';
		
		$ch = curl_init('https://www.bungie.net' .$remotePath);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($ch);
		//curl_exec($ch);
		
		//echo 'Curl error: ' .curl_error($ch). '<br/>';
		echo 'Writing local file...</br>';
		
		file_put_contents($localPath. '.zip', $data);
		echo 'Extracting archive...</br>';
		
		$zip = new ZipArchive();
		if ($zip->open($localPath.'.zip') === TRUE) {
			//$zip->extractTo('.');
			//echo 'Extracting to ' .pathinfo($localPath, PATHINFO_DIRNAME). '</br>';
			$zip->extractTo(pathinfo($localPath, PATHINFO_DIRNAME));
			$zip->close();
		}
		
		curl_close($ch);
	}
	
	function get_manifest_path ($apiRoot, $apiKey, $serverRoot) {
		//Retrieve the current manifest file name
		$url = $apiRoot. 'Destiny/Manifest/';
		//echo $url. '</br>'; //Troubleshooting
		
		$json = execute_curl($url, $apiKey);
		
		//var_dump($json); //Troubleshooting;
		
		$path = $json['Response']['mobileWorldContentPaths']['en'];
		//echo $path. '</br>'; //Troubleshooting

		//$localPath = $serverRoot;
		//echo '{"text":1. ' .$localPath. '"}';
		//$localPath .= pathinfo($path, PATHINFO_BASENAME);
		$localPath = pathinfo($path, PATHINFO_BASENAME);
		//echo $localPath. '<br/>'; //Troubleshooting
		//echo '{"text":2. ' .$localPath. '"}';
		
		return $path;
	}

	function open_manifest_connection($path) {
		echo 'Opening connection to manifest ' .$path. '</br>';
		return new SQLite3($path);
		
	}

	function get_manifest_query_results($conn, $sql) {
		//echo $sql. '<br/>'; //Troubleshooting
		$results = $conn->query($sql);
		
		return $results;
	}

	function get_manifest_table_data($dbconn, $tableName) {
		$table = $dbconn->query('SELECT * FROM ' .$tableName);
		return $table;
	}
	
	function get_manifest_table_record_count($dbconn, $tableName) {
		$sql = 'SELECT COUNT(*) record_count FROM ' .$tableName;
		//echo $sql. '</br>';
		
		$query = $dbconn->query($sql);

		$row = $query->fetchArray();
		
		return $row['record_count'];
	}
	
	function load_manifest_table_data($tableName, $table, $dbconn) {

		$counter = 0;
		$expected = 0;
		
		while ($row = $table->fetchArray()) {
			
			$json = json_decode($row['json'],true);
			
			switch ($tableName) {
/*				case 'DestinyActivityBundleDefinition';
					$values = '("' .$json['bundleHash']. '", ';
					$values .= '"' .$json['activityName']. '", ';
					$values .= '"' .str_replace('"', '\"', $json['activityDescription']). '", ';
					//Skipped icon
					//Skipped releaseIcon
					//Skipped releaseTime
					$values .= '"' .$json['destinationHash']. '", ';
					$values .= '"' .$json['placeHash']. '", ';
					$values .= '"' .$json['activityTypeHash']. '")';
					//Skipped activityHashes []
					//Skipped hash
					//Skipped index
					
					$insertSQL = 'insert into `manifest`.`DestinyActivityBundleDefinition` (';
					$insertSQL .= 'bundleHash, activityName, activityDescription, ';
					$insertSQL .= 'destinationHash, placeHash, activityTypeHash) values ';
					$insertSQL .= $values;

					//echo $insertSQL. '<br/>'; //Troubleshooting
						
					mysqli_query($dbconn, $insertSQL);
					$counter += mysqli_affected_rows($dbconn);

					break;*/
				/*case 'DestinyActivityDefinition';
					
					$values = '(' .$json['activityHash']. ', ';
					$values .= '\'' .str_replace("'", "''", $json['activityName']). '\', ';
					$values .= '\'' .str_replace("'", "''", $json['activityDescription']). '\', ';
					//Skipped icon
					//Skipped releaseIcon
					//Skipped releaseTime
					$values .= $json['activityLevel']. ', ';
					//Skipped completionFlagHash
					$values .= $json['activityPower']. ', ';
					$values .= $json['minParty']. ', ';
					$values .= $json['maxParty']. ', ';
					$values .= $json['maxPlayers']. ', ';
					$values .= $json['destinationHash']. ', ';
					$values .= $json['placeHash']. ', ';
					$values .= $json['activityTypeHash']. ', ';
					//Skipped tier
					//Skipped pgcrImage
					//Skipped rewards[]
					//modifiers
					$skulls = null;
					
					if (count($json['skulls']) > 0) {
						foreach ($json['skulls'] as $skull) {
							if (is_null($skulls)) {
								$skulls .= $skull['displayName'];
							} else {
								$skulls .= ';' .$skull['displayName'];
							}
						}
						$skulls = count($json['skulls']). ';' .$skulls;
						echo $skulls. '</br>';
						$values .= "'" .$skulls. "')";
					} else {
						$values .= 'NULL)';
					}
					
					//Skipped isPlaylist
					//Skipped hash
					//Skipped index
					//echo $values. '<br/>'; //Troubleshooting
					
					$insertSQL = 'INSERT INTO manifest.destinyactivitydefinition ';
					$insertSQL .= '(activityHash, activityName, activityDescription, ';
					$insertSQL .= 'activityLevel, activityPower, minParty, maxParty, ';
					$insertSQL .= 'maxPlayers, destinationHash, placeHash, activityTypeHash, modifiers) VALUES ';
					$insertSQL .= $values;
					
					//echo $insertSQL. '<br/>'; //Troubleshooting
					
					$insertResult = pg_query($dbconn, $insertSQL);

					$counter += pg_affected_rows($insertResult);

					$expected++;
					
					//mysqli_query($dbconn, $insertSQL);
					//$counter += mysqli_affected_rows($dbconn);
					
					break;*/

				/*case 'DestinyActivityTypeDefinition';
					$values = '(' .$json['activityTypeHash']. ', ';
					$values .= "'" .$json['identifier']. "', ";
					$values .= "'" .str_replace("'", "''", $json['activityTypeName']). "', ";
					$values .= "'" .str_replace("'", "''", $json['activityTypeDescription']). "')";
					//Skipped icon
					//Skipped activeBackgroundVirtualPath
					//Skipped completedBackgroundVirtualPath
					//Skipped hiddenOverrideVirtualPath
					//Skipped tooltipBackgroundVirtualPath
					//Skipped enlargedActiveBackgroundVirtualPath
					//Skipped enlargedCompletedBackgroundVirtualPath
					//Skipped enlargedHiddenOverrideVirtualPath
					//Skipped enlargedTooltipBackgroundVirtualPath
					//Skipped order
					//Skipped hash
					//Skipped index

					//echo $values. '<br/>'; //Troubleshooting

					$insertSQL = 'insert into manifest.destinyactivitytypedefinition ';
					$insertSQL .= '(activitytypehash, identifier, activitytypename, ';
					$insertSQL .= 'activitytypedescription) values ';
					$insertSQL .= $values;
						
					echo $insertSQL. '<br/>';
						
					//mysqli_query($dbconn, $insertSQL);
					//$counter += mysqli_affected_rows($dbconn);
					$insertResult = pg_query($dbconn, $insertSQL);
					
					echo pg_last_error($dbconn). '</br>';
					$counter += pg_affected_rows($insertResult);
								
					break;*/
/*				case 'DestinyClassDefinition';
					//var_dump($json);
					$values = '("' .$json['classHash']. '", ';
					$values .= $json['classType']. ', ';
					$values .= '"' .$json['className']. '", ';
					$values .= '"' .$json['classNameFemale']. '", ';
					$values .= '"' .$json['classNameMale']. '", ';
					$values .= '"' .$json['classIdentifier']. '", ';
					$values .= '"' .$json['mentorVendorIdentifier']. '", ';
					$values .= '"' .$json['hash']. '", ';
					$values .= $json['index']. ')';

					$insertSQL = 'insert into `manifest`.`' .$tableName. '` values ' .$values;
					
					echo $insertSQL. '<br/>'; //Troubleshooting
					
					mysqli_query($dbconn, $insertSQL);
					$counter += mysqli_affected_rows($dbconn);
					
					break;*/
/*
				case 'DestinyDestinationDefinition';
					$values = '("' .$json['destinationHash']. '", ';
					$values .= '"' .$json['destinationName']. '", ';
					//Skipped icon
					$values .= '"' .$json['placeHash']. '", ';
					$values .= '"' .$json['destinationIdentifier']. '")';
					//Skipped hash
					//Skipped index
						
					$insertSQL = 'insert into `manifest`.`DestinyDestinationDefinition` (';
					$insertSQL .= 'destinationHash, destinationName, placeHash, ';
					$insertSQL .= 'destinationIdentifier) values ';
					$insertSQL .= $values;
					
					//echo $insertSQL. '<br/>'; //Troubleshooting
					
					mysqli_query($dbconn, $insertSQL);
					$counter += mysqli_affected_rows($dbconn);
					
					break;*/
				case 'DestinyHistoricalStatsDefinition';
					$values = '(\'' .$json['statId']. '\', ';
					$values .= $json['group']. ', ';
					
					//Skipped periodTypes
					//Skipped modes
					$modes = $json['modes'];
					
					$values .= $json['category']. ', ';
					$values .= '\'' .$json['statName']. '\', ';
					
					if (array_key_exists('statDescription', $json)) {
						$values .= '\'' .str_replace("'", "''", $json['statDescription']). '\', ';
					}

					if (array_key_exists('iconImage', $json)) {
						$values .= '\'' .$json['iconImage']. '\', ';
					} else {
						$values .= 'null, ';
					}

					$values .= $json['unitType']. ')';
					//Skipped unitLabel
					//Skipped weight
					
					$insertSQL = 'insert into manifest.destinyhistoricalstatsdefinition (';
					
					if (array_key_exists('statDescription', $json)) {
						$insertSQL .= 'statkey, statgroup, statcategory, statname, statdescription, iconimage, unittype) values ';
					} else {
						$insertSQL .= 'statkey, statgroup, statcategory, statname, iconimage, unittype) values ';
					}

					$insertSQL .= $values;
					
					echo $insertSQL. '<br/>'; //Troubleshooting

					//mysqli_query($dbconn, $insertSQL);
					$insertQuery = pg_query($dbconn, $insertSQL);
					//$counter += mysqli_affected_rows($dbconn);
					$counter += pg_affected_rows($insertQuery);
					
					//$lastInsertId = mysqli_insert_id($dbconn);
					//echo $lastInsertId. '<br/>';
					
					//foreach ($modes as $mode) {
						//$sql = 'insert into manifest.dhsdModes values (';
						//$sql .= $lastInsertId. ',' .$mode. ')';
						
						//echo $sql. '<br/>';
						
						//mysqli_query($dbconn, $sql);
						//echo $type. '<br/>';
					//}

					break;

/*				case 'DestinyPlaceDefinition';
					$values = '("' .$json['placeHash']. '", ';
					$values .= '"' .$json['placeName']. '", ';
					$values .= '"' .$json['placeDescription']. '")';
					//Skipped icon
					//Skipped hash
					//Skipped index
					
					$insertSQL = 'insert into `manifest`.`DestinyPlaceDefinition` (';
					$insertSQL .= 'placeHash, placeName, placeDescription) values ';
					$insertSQL .= $values;
						
					//echo $insertSQL. '<br/>'; //Troubleshooting
						
					mysqli_query($dbconn, $insertSQL);
					$counter += mysqli_affected_rows($dbconn);
						
					break;*/

/*				case 'DestinyRecordBookDefinition';
					//displayName
					//displayDescription
					echo $json['displayName']. ' - ' .$json['displayDescription']. '</br>';
					
					foreach ($json['pages'] as $page) {
						echo $page['displayName']. ' - ' .$page['displayDescription']. '</br>';

						foreach ($page['records'] as $record)
							echo $record['recordHash']. '</br>';
					}
					//Skip icon
					//Skip unavailableReason
					//Skip progressionHash
					//Skip hash
					//index
						
					//What to do with pages?
					//Create table drbdPages?
					//Page number (auto increment integer)
					//displayName
					//displayDescription
					//displayStyle
					//What to do with records?
					//Create table to intersect between DestinyRecordBookDefinition and pages
					break;*/

				/*case 'DestinyRecordDefinition';
					if (array_key_exists('displayName', $json)) {
						$values = '(' .$row['id']. ', ';
						$values .= '"' .$json['displayName']. '", ';
						$values .= '"' .str_replace('"', '', $json['description']). '")';
						//echo $row['id']. '</br>'; //Troubleshooting
						//echo $values. '</br>'; //Troubleshooting
						//Skip recordValueUIStyle
						//Skip icon
						//Skip rewards()
						//Skip objectives()
						//Skip hash
						//Skip index
						
						//echo $json['displayName']. ' - ' .$json['description']. '</br>';
						//Create DestinyRecordDefinition-DestinyObjectiveDefinition intersection?
						//objectiveHash
						//foreach ($json['objectives'] as $objective)
							//echo $objective['objectiveHash']. '</br>';
						$insertSQL = 'insert into `manifest`.`DestinyRecordDefinition` (';
						$insertSQL .= 'id, displayName, description) values ';
						$insertSQL .= $values;
						
						//echo $insertSQL. '<br/>'; //Troubleshooting
						
						mysqli_query($dbconn, $insertSQL);
						$counter += mysqli_affected_rows($dbconn);
					}
					break;*/
				/*case 'DestinyScriptedSkullDefinition':
					$values = '(' .$json['skullHash']. ',';
					$values .= '\'' .$json['identifier']. '\',';
					$values .= '\'' .$json['skullName']. '\',';
					$values .= '\'' .$json['description']. '\',';
					$values .= '\'' .$json['iconPath']. '\')';
					//Skip hash
					//Skip index
					//Skip redacted
					
					echo $values. '</br>';
					
					$insertSQL = 'insert into manifest.destinyscriptedskulldefinition (';
					$insertSQL .= 'skullhash, identifier, skullname, description, iconpath) values ';
					$insertSQL .= $values;
					
					echo $insertSQL. '</br>';
					
					$insertQuery = pg_query($dbconn, $insertSQL);
					$counter += pg_affected_rows($insertQuery);
					
					break;*/
			}
		}

		echo 'Expected count ' .$expected. '</br>';
		echo 'Processed count ' .$counter. '</br>';
		
		return $counter;
	}
	
	function get_manifest_tables ($dbconn) {

		$tableSQL = 'select tbl_name
			from sqlite_master
			where type = "table"
			order by tbl_name';
		
		$i = 0;
		
		$result = $dbconn->query($tableSQL);

		while($row = $result->fetchArray()) {
			//var_dump($row);
			$tables[$i] = $row['tbl_name'];
			$i++;
		}
		return $tables;
	}
	
	function get_manifest_db_tables ($dbconn) {
		$manifestSchema = 'manifest';
		
		$tableSQL = 'SELECT table_name
			FROM `information_schema`.`tables`
			WHERE TABLE_SCHEMA = "manifest"
			ORDER BY table_name';

		//Open database connection
		//$dbconn = open_db_connect($dbhost, $dbuser, $dbpwd, $dbstg);
	
		$results = mysqli_query($dbconn, $tableSQL);
		$i = 0;
		
		while ($table = mysqli_fetch_assoc($results)) {
			$tables[$i] = $table['table_name'];
			$i++;
		}
		//Close the database connection
		//close_db_connect($dbconn);
		
		return $tables;
	}
	
	function compare_schemas ($tables, $dbTables) {
		$i = count($tables);
		$j = count($dbTables);
	}

	function get_remote_manifest_definition ($apiKey, $apiRoot, $type, $hash) {
		$url = $apiRoot. 'Destiny/Manifest/' .$type. '/' .$hash. '/';
		$json = execute_curl($url, $apiKey);
		
		return $json;
	}
	
	function get_manifest_table_layout($dbconn, $table) {

		$pragmaSQL = 'PRAGMA table_info(' .$table. ')';
		
		$pragma = $dbconn->query($pragmaSQL);
		
		while($row = $pragma->fetchArray()) {
				
			$pk = $row['pk'];
				
			if ($pk) {
				echo 'Primary key found...</br>';
				$dataType = $row['type'];
		
				if ($dataType == 'INTEGER') {
					$stdLayout = true;
					echo $stdLayout. '</br>';
				} else {
					$stdLayout = 0;
					echo 'Non-standard layout...</br>';
				}
			}
		}
		
		return $stdLayout;
	}
?>