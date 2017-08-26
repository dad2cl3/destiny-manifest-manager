# destiny-manifest-manager
Database and Javascript scripts used to load the Destiny SQLite manifest file to a Postgres database.

**Database setup**

Documentation on setting up Postgres can be found at https://www.postgresql.org.

The manifest data is loaded in the manifest schema.

All database scripts and sample queries are located in the sql folder.

**Bungie.net**

The API calls to bungie.net require a developer API key. The key can be acquired by visiting https://www.bungie.net/en/Application and documentation of the APIs can be found at http://destinydevs.github.io/BungieNetPlatform/

**Documentation**

The doc folder contains the basic data model for setting up the manifest loader. The loader will create additional tables as needed that are not represented in the data model. The data model was created using Navicat Data Modeler which can be found at https://www.navicat.com/products/navicat-data-modeler. A PNG version of the data model has also been uploaded to the repository.

The doc folder also contains the process flow for the manifest loader. The process flow was created in the draw.io desktop Chrome application. The application can be found at https://chrome.google.com/webstore/detail/drawio-desktop/pebppomjfocnoigkeepgbmcifnnlndla?utm_source=chrome-ntp-icon A PNG version of the process flow has also been uploaded to the repository.
