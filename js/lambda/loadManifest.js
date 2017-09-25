'use strict';

var fs = require('fs');
var sqlite = require('sqlite3');
var pg = require('pg');
var AWS = require('aws-sdk');
var config = require('./config');



//Setup the S3 connection
var s3 = new AWS.S3(config.aws);

var sql = {
    truncate : `TRUNCATE TABLE ${config.db.schema}.${config.db.stageTable}`,
    stage : `INSERT INTO ${config.db.schema}.${config.db.stageTable} (table_name, pk, json) VALUES ($1, $2, $3)`,
    promote : `SELECT ${config.db.schema}.fn_load_manifest($1)`

}

var filename = '';
var manifest;

var pgPool = new pg.Pool(config.pgPoolOptions);

function truncateData(event) {

    return pgPool.connect()
        .then(client => {
            return client.query(sql.truncate)
                .then(res => {
                    client.release();
                    return event;
                })
                .catch(err => { client.release(); console.error(err.stack); })
        });
}

function getManifest(event) {
    console.log('Downloading manifest...');

    var params = {
        Bucket : event.data.Bucket,
        Key : event.data.Key
    };
    console.log(params);

    var tmpFile = fs.createWriteStream(`/tmp/${filename}`);

    tmpFile.on('close', function downloadComplete() {
        console.log('Download complete');
    });

    return new Promise (function (resolve, reject) {
        return s3.getObject(params).createReadStream().on('end', () => { console.log('End of read stream'); resolve(); }).on('error', (error) => { reject(error); }).pipe(tmpFile);
    });
}

function openManifest() {
    console.log('Opening manifest...');
    console.log(filename);

    //return sqlite.open(`/tmp/${filename}`);
    manifest = new sqlite.Database(`/tmp/${filename}`);

    return manifest;
}

function getManifestTables(manifest) {
    console.log('Retrieving manifest data dictionary...');

    var tableList = 'select tbl_name from sqlite_master where type = "table" order by tbl_name';

    //return sqlite.each(tableList)
    return new Promise (function (resolve, reject) {
        manifest.all(tableList, function (err, rows) {
            console.log('Data dictionary...');

            resolve(rows);
        });
    });
}


function getManifestData(tables) {
    console.log('Processing tables...');
    console.log(`Table count: ${tables.length}`);

    var queries = [];

    return new Promise (function (resolve, reject) {
        for (var i = 0; i < tables.length; i++) {
            console.log(tables[i].tbl_name);

            queries.push(getTableData(tables[i].tbl_name));
        }

        Promise.all(queries)
            .then(function (results) {
                console.log(`Results: ${results.length}`);
                resolve(results);
            });
    });

}

function getTableData(table) {
    console.log(`Retrieving data from ${table}...`);

    var sql = `SELECT * FROM ${table}`;

    return new Promise (function (resolve, reject) {
        manifest.all(sql, function (err, rows) {
            if (err) reject(err);
            console.log(rows.length);
            if (rows) {
                var data = {
                    table : table,
                    rows : rows
                }
                resolve(data);
            }
        });
    });
}

function prepareData(data) {
    console.log('Building inserts...');
    console.log(`Length: ${data.length}`);

    var records = [];

    return new Promise (function (resolve, reject) {

        data.forEach(function readTables(table) {
            table.rows.forEach(function readRows(row) {
                var record = { table : table.table, data : row.json };

               if (row.id) {
                   record.pk = { id : row.id };
               } else {
                   record.pk = { key : row.key };
               }
               records.push(record);
            });
        });

        resolve(records);
    });
}


function stageManifestData(data) {
    console.log('Loading manifest data...');
    console.log(data.length);

    var queries = [];
    var insertCount = 0;

    return new Promise (function (resolve, reject) {
        data.forEach(function readData(record) {
            queries.push(
                pgPool.connect()
                    .then(client => {
                        return client.query(sql.stage, [record.table, record.pk, record.data])
                            .then(res => {
                                client.release();
                                insertCount += res.rowCount;
                                return res.rowCount;
                            })
                            .catch(err => { client.release(); console.error(err.stack); })
                    })
            );
        });

        Promise.all(queries).then(function (results) {
            resolve(insertCount);
        });
    });
}

exports.handler = function (event, context, callback) {
    context.callbackWaitsForEmptyEventLoop = false;

    filename = `${event.data.Key.substring(event.data.Key.lastIndexOf('/') + 1)}`;

    console.log(`Processing file: ${filename}`);

    Promise.resolve(event)
        .then(truncateData)
        .then(getManifest)
        .then((insertCount) => { callback(null, `${insertCount} manifest items loaded to database.`) })
    ;
}
