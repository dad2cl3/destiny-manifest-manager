//PostgreSQL configuration
exports.pgPoolOptions = {
    'host': 'YOUR-DATABASE-HOST',
    'port': YOUR-DATABASE-PORT,
    'user': 'YOUR-DATABASE-USER',
    'password': 'YOUR-DATABASE-PASSWORD',
    'database': 'YOUR-DATABASE'
};

exports.db = {
    schema : 'manifest',
    versionLog : 't_manifest_version',
    dataTable : 't_manifest'
};

//Bungie.net configuration
exports.apiKey = 'YOUR-API-KEY';
exports.manifestUrl = `https://www.bungie.net/Platform/Destiny2/Manifest/`;

//AWS configuration
exports.aws = {
    accessKeyId : 'YOUR-AWS-ACCESS-KEY-ID',
    secretAccessKey : 'YOUR-AWS-SECRET-ACCESS-KEY',
    region : 'YOUR-AWS-REGION'
};