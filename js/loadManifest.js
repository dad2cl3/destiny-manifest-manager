'use strict';

var sqlite = require('sqlite');
var pg = require('pg');

//Setup the database connection pool
var pgConfig = {
    host : 'YOUR-DATABASE-SERVER',
    port : 5432,
    database : 'YOUR-DATABASE',
    user : 'YOUR-DATABASE-USER',
    password : 'YOUR-DATABASE-PASSWORD',
    max : 20
};

var db = {
    schema : 'manifest',
    manifestTable : 't_manifest'
}

var sql = {
    truncate : `TRUNCATE TABLE ${db.schema}.${db.manifestTable}`,
    insert : `INSERT INTO ${db.schema}.${db.manifestTable} (table_name, pk, json) VALUES ($1, $2, $3)`
}

var pgPool = new pg.Pool(pgConfig);

function truncateData() {

    return pgPool.connect()
        .then(client => {
            return client.query(sql.truncate)
                .then(res => {
                    client.release();
                })
                .catch(err => { client.release(); console.error(err.stack); })
        });
}

function openManifest() {
    console.log('Opening manifest...');
    console.log(fileName);

    return sqlite.open(fileName);
}

function closeManifest() {
    console.log('Closing manifest...');

    return new Promise (function (resolve, reject) {
        resolve(manifest.close());
    });
}

function getManifestTables() {
    console.log('Retrieving manifest data dictionary...');

    var tableList = 'select tbl_name from sqlite_master where type = "table" order by tbl_name';

    return sqlite.all(tableList);
}

function processManifestTables(tables) {
    console.log('Processing tables...');

    tables.forEach(function readTables(table) {
        //console.log(`Processing table ${table.tbl_name}...`);
        sqlite.all(`select * from ${table.tbl_name}`)
            .then(res => {
                var inserts = [];

                res.forEach(function readRows(row) {
                    if (row.id) {
                        //console.log(`${table.tbl_name} - ${row.id}`);

                        var insert = {
                            tableName : table.tbl_name,
                            pk : { id : row.id },
                            json : row.json
                        };

                        inserts.push(insert);
                    } else {
                        var insert = {
                            tableName : table.tbl_name,
                            pk : { key : row.key },
                            json : row.json
                        };

                        inserts.push(insert);
                    }
                });
                return processManifestInserts(inserts);
            });
    });
}

function processManifestInserts(inserts) {
    //console.log(inserts.length);

    var queries = [];

    inserts.forEach(function readInserts(insert) {
        queries.push(
            pgPool.connect()
                .then(client => {
                    return client.query(sql.insert, [insert.tableName, insert.pk, insert.json])
                        .then(res => {
                            client.release();
                        })
                        .catch(err => { client.release(); console.error(err.stack); })
                })
        );
    });

    return Promise.all(queries);

}

var filePathName = 'https://www.bungie.net/common/destiny_content/sqlite/en/world_sql_content_b6c7590005d9365b2723f8995f361e3f.content';
console.log(`File path and name: ${filePathName}`);

var fileName = filePathName.substring(filePathName.lastIndexOf('/') + 1);
console.log(`File name: ${fileName}`);

truncateData()
    .then(openManifest)
    .then(getManifestTables)
    .then(processManifestTables)
    ;
