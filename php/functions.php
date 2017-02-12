<?php

	function execute_cURL ($url, $apiKey) {
	
		$ch = curl_init($url);
	
		//echo 'execute_curl URL ' .$url. '</br>'; //Troubleshooting
	
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-API-Key:' . $apiKey));
	
		$json = json_decode(curl_exec($ch), true);
		//echo 'API Error = ' .$json['ErrorStatus']. '<br/>'; //Troubleshooting
	
		if (stristr($json['ErrorStatus'],'Throttle')) {
			//Pause processing for 10s
			echo 'Processing paused for 10s...' . '<br/>';
			sleep(10);
			//Attempt the URL
			$json = execute_curl($url);
		}
	
		curl_close($ch);
	
		return $json;
	}

	function open_db_connect($host, $port, $dbi, $user, $passwd) {
		$connectionString = 'host=' .$host;
		$connectionString .= ' port=' .$port;
		$connectionString .= ' dbname=' .$dbi;
		$connectionString .= ' user=' .$user;
		$connectionString .= ' password=' .$passwd;

		//echo $connectionString. '</br>'; //Troubleshooting

		$dbconn = pg_connect($connectionString) or die ('Unable to connect to ' .$dbi. '</br>');

		return $dbconn;
	}

	function close_db_connect($dbconn) {
		pg_close($dbconn);
	}

?>