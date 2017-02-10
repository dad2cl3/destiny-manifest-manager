# destiny-manifest-manager
Database and PHP scripts used to load the Destiny SQLite manifest file to a Postgres database.

**Database setup**

Documentation on setting up Postgres can be found at https://www.postgresql.org.

The manifest data is managed in the manifest schema.

All database scripts are located in the sql folder.

**Bungie.net**

The API calls to bungie.net require a developer API key. The key can be acquired by visiting https://www.bungie.net/en/Application and documentation of the APIs can be found at http://destinydevs.github.io/BungieNetPlatform/
