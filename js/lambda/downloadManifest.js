'use strict';

var fetch = require('node-fetch');
//var fs = require('fs');
var AWS = require('aws-sdk');
var config = require('./config');

var s3 = new AWS.S3(config.aws);

var unzipper = require('unzipper');

function downloadManifest (path) {
	console.log('Downloading...');

	/* works to download and extract locally */
	/*return fetch(path)
	.then(res => {
		console.log(`Status: ${res.status}`);
		res.body.pipe(unzipper.Extract({ path: '/tmp/' }));
		return res.status;
	});*/

	
	/* works to download and extract locally */
	/*return fetch(path)
	.then(res => {
		var outFile = fs.createWriteStream(fileName);
		res.body.pipe(unzipper.Parse())
		.on('entry', function (entry) {
			entry.pipe(outFile);
		});
	});*/

	var fileName = path.substring(path.lastIndexOf('/') + 1);
	console.log(`Downloading ${fileName}`);
    console.log(path);

    return fetch(path)
		.then(res => {
		    console.log(`Status: ${res.status}`);

		    if (res.status === 200) {
		        return new Promise (function unzipManifest (resolve, reject) {
		            res.body.pipe(unzipper.Parse()).on('entry', function (entry) {
                        console.log('Unzipping file...');
                        console.log(`Type: ${entry.type}`);
                        console.log(`File name: ${entry.path}`);

                        var params = {
                            ACL : config.s3.acl,
                            Bucket : config.s3.bucket,
                            Key : `public/${fileName}`,
                            ContentType : 'binary',
                            Body : entry
                        };

                        return s3.upload(params, function upload(err, data) {
                            console.log('Uploading file...');
                            if (err) {
                                console.error(`Error: ${err.stack}`);
                                reject(err);
                            } else if (data) {
                                console.log(`Data: ${JSON.stringify(data)}`);
                                resolve(data);
                            }
                        });
                    }).on('error', function (err) {
                        console.error(err.stack);
                        //return err;
                    }).on('finish', function (entry) {
                        console.log('I am finished');
                    });
                });
		    }
        });
}

exports.handler = function (event, context, callback) {

	console.log(`Event: ${JSON.stringify(event)}`);

	if (event.download) {
		var path = `https://www.bungie.net${event.path}`;
		console.log(`Path: ${path}`);

		Promise.resolve(path)
			.then(downloadManifest)
			//.then((data) => { console.log({ path : event.path, data : data }) });
			.then((data) => { callback(null, { path : event.path, manifestId : event.manifestId, data : data }); });
	} else {
		//callback(null, { download : event.download });
		console.log({ download: event.download });
	}
}
