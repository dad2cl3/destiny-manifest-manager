<?php

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
		
		$zip = new ZipArchive() or die ('Unable to create ZipArchive object.</br>');
		
		if ($zip->open($localPath.'.zip') === TRUE) {
			//$zip->extractTo('.');
			echo 'Extracting to ' .pathinfo($localPath, PATHINFO_DIRNAME). '</br>';
			$zip->extractTo(pathinfo($localPath, PATHINFO_DIRNAME));
			$zip->close();
		} else {
			echo 'Something went wrong opening ' .$localPath. '.zip</br>';
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