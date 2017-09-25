'use strict';

//const { fetch } = require('node-fetch');
var fetch = require('node-fetch');
const { Pool } = require('pg');
var config = require('./config');

var sql = {
	update : `UPDATE ${config.db.schema}.${config.db.versionLog} SET current = FALSE WHERE current = TRUE`,
	insert : `INSERT INTO ${config.db.schema}.${config.db.versionLog} (version) VALUES ($1) RETURNING manifest_id`
}

const pgPool = new Pool(config.pgPoolOptions);

//API globals
var header = { 'X-API-Key': config.apiKey };

function getApiManifest () {
	
	return fetch(config.manifestUrl, { 'headers': header })
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
			//console.log(manifest);

			return manifest;
	});
}

function getDbManifest(manifest) {

	var selectSql = `SELECT version FROM ${config.db.schema}.${config.db.versionLog} WHERE current = TRUE`;
	
	return pgPool.query(selectSql)
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

		return { download : false };
	} else {
		//New manifest found
		console.log(`Manifest version ${manifest.api.version} available for download.`);

		return pgPool.query(config.sql.update)
			.then(res => {
				return pgPool.query(config.sql.insert, [manifest.api])
					.then(res => {
						return { download : true, manifestId : res.rows[0].manifest_id, path : manifest.api.path };
					})
					.catch(err => { console.error(err.stack); })
			})
			.catch(err => { console.error(err.stack); });
	};
}

exports.handler = function (event, context, callback) {

    context.callbackWaitsForEmptyEventLoop = false;

	getApiManifest()
		.then(getDbManifest)
		.then(checkVersions)
		.then((manifest) => { callback(null, manifest); })
	;
}