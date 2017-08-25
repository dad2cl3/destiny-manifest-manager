'use strict';

var fetch = require('node-fetch');

var unzipper = require('unzipper');

function downloadManifest (path) {
	console.log('Downloading...');

	/* works to download and extract locally */
	return fetch(path)
	.then(res => {
		res.body.pipe(unzipper.Extract({ path: './' }));
		return res.status;
	});
}

	var path = `https://www.bungie.net/common/destiny_content/sqlite/en/world_sql_content_b6c7590005d9365b2723f8995f361e3f.content`;
	console.log(`Path: ${path}`);

	downloadManifest(path);
