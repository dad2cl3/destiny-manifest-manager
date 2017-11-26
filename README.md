# destiny-manifest-manager
Database and Javascript scripts used to load the Destiny SQLite manifest file to a Postgres database.

**Database setup**

Documentation on setting up Postgres can be found [here](https://www.postgresql.org).

The manifest data is loaded in the manifest schema.

All database scripts and sample queries are located in the sql folder.

**Bungie.net**

The API calls to bungie.net require a developer API key. The key can be acquired by visiting https://www.bungie.net/en/Application and documentation of the APIs can be found [here](https://bungie-net.github.io/multi/index.html).

**Documentation**

The doc folder contains the basic data model for staging and processing the manifest. The data model was created using Navicat Data Modeler which can be found [here](https://www.navicat.com/products/navicat-data-modeler). A PNG version of the data model has also been uploaded to the repository and is shown below.

![alt text](https://github.com/dad2cl3/destiny-manifest-manager/blob/master/doc/destiny-manifest-manager-v2.png "Data Model")

The doc folder also contains the process flow for the manifest loader. The process flow was created in the draw.io desktop Chrome application. The application can be found [here](https://chrome.google.com/webstore/detail/drawio-desktop/pebppomjfocnoigkeepgbmcifnnlndla?utm_source=chrome-ntp-icon). A PNG version of the process flow has also been uploaded to the repository and is shown below.

![alt text](https://github.com/dad2cl3/destiny-manifest-manager/blob/master/doc/destiny-manifest-manager-v2-process.png "Process Flow")
