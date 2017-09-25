//PostgreSQL configuration
exports.pgPoolOptions = {
    'host': 'YOUR-DATABASE-HOST',
    'port': YOUR-DATABASE-PORT,
    'user': 'YOUR-DATABASE-USER',
    'password': 'YOUR-DATABASE-PASSWORD',
    'database': 'YOUR-DATABASE'
};

exports.db = {
    schema : 'YOUR-SCHEMA',
    versionLog : 'YOUR-VERSION-TABLE',
    stageTable : 'YOUR-STAGE-TABLE',
    dataTable : 'YOUR-TABLE'
};

//Bungie.net configuration
exports.apiKey = 'YOUR-API-KEY';
exports.manifestUrl = `https://www.bungie.net/Platform/Destiny2/Manifest/`;

//AWS configuration
exports.aws = {
    accessKeyId : 'YOUR-ACCESS-KEY-ID',
    secretAccessKey : 'YOUR-SECRET-ACCESS-KEY',
    region : 'YOUR-AWS-REGION'
};

exports.s3 = {
    bucket : 'YOUR-BUCKET',
    acl : 'public-read'
}