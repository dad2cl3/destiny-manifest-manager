'use strict';

//const { fetch } = require('node-fetch');
var fetch = require('node-fetch');

const { Client } = require('pg');

//Configure database options
var pgClientOptions = {
	'host': 'YOUR-DATABASE-SERVER',
	'port': 5432,
	'user': 'YOUR-DATABASE-USER',
	'password': 'YOUR-DATABASE-PASSWORD',
	'database': 'YOUR-DATABASE'
}

var db = {
	schema : 'manifest',
	versionLog : 't_manifest_version',
	dataTable : 't_manifest'
}

var sql = {
	update : `UPDATE ${db.schema}.${db.versionLog} SET current = FALSE WHERE current = TRUE`,
	insert : `INSERT INTO ${db.schema}.${db.versionLog} (version) VALUES ($1)`
}

const pgClient = new Client(pgClientOptions);
pgClient.connect();

//API globals
var apiKey = 'YOUR-API-KEY';
var header = { 'X-API-Key': apiKey };
//var manifestUrl = `https://www.bungie.net/Platform/Destiny2/Manifest/`;
var manifestUrl = `https://www.bungie.net/Platform/Destiny/Manifest/`;

function getApiManifest () {
	
	return fetch(manifestUrl, { 'headers': header })
		//.then(res => { console.log(res); })
		.then(res => res.json())
		.then(json => { 

			var currentVersion = json.Response.version;
			var currentPath = json.Response.mobileWorldContentPaths.en;

			var manifest = {
				'api': {
					'version': currentVersion,
					'path': currentPath
				}
			};

			return manifest;
	});
}

function getDbManifest(manifest) {
	
	var selectSql = `SELECT version FROM ${db.schema}.${db.versionLog} WHERE current = TRUE`;
	
	return pgClient.query(selectSql)
	.then(result => {

		var rowCount = result.rowCount;
		var rows = result.rows;
		
		if (rowCount === 1) {
			manifest.db = {
				'version': rows[0].version.version
			};
		} else if (rowCount === 0) {
			manifest.db = {
				version : 0
			}
		};

		return manifest;
	});
}

function checkVersions (manifest) {
	console.log('Comparing versions...');

	if (manifest.api.version === manifest.db.version) {
		//Old manifest
		console.log(`Manifest version ${manifest.api.version} already downloaded.`);

		pgClient.end();
		return { download : false };
	} else {
		//New manifest found
		console.log(`Manifest version ${manifest.api.version} available for download.`);
		//console.log(manifest.api);

		return pgClient.query(sql.update)
			.then(res => {
				//console.log(res.rowCount);
				return pgClient.query(sql.insert, [manifest.api])
					.then(res => {
						//console.log(res.rowCount);
						pgClient.end();
						return { download : true, path : manifest.api.path };
					})
					.catch(err => { pgClient.end(); console.error(err.stack); })
			})
			.catch(err => { pgClient.end(); console.error(err.stack); });
	};
}

	getApiManifest()
		.then(getDbManifest)
		.then(checkVersions)
	;