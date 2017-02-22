# destiny-manifest-manager
Database and PHP scripts used to load the Destiny SQLite manifest file to a Postgres database.

**Database setup**

Documentation on setting up Postgres can be found at https://www.postgresql.org.

The manifest data is staged and managed in the manifest schema.

All database scripts are located in the sql folder.

**Bungie.net**

The API calls to bungie.net require a developer API key. The key can be acquired by visiting https://www.bungie.net/en/Application and documentation of the APIs can be found at http://destinydevs.github.io/BungieNetPlatform/

**Documentation**

The doc folder contains the basic data model for setting up the manifest loader. The loader will create additional tables as needed that are not represented in the data model. The data model was created using Navicat Data Modeler which can be found at https://www.navicat.com/products/navicat-data-modeler. A PNG version of the data model has also been uploaded to the repository.

The doc folder also contains the process flow for the manifest loader. The process flow was created in the draw.io desktop Chrome application. The application can be found at https://chrome.google.com/webstore/detail/drawio-desktop/pebppomjfocnoigkeepgbmcifnnlndla?utm_source=chrome-ntp-icon A PNG version of the process flow has also been uploaded to the repository.

**Adding parsed columns**

The Postgres function fn_add_column adds an additional column to the appropriate table.

The function takes five parameters:

  Source table - The manifest table from which the new field will be sourced.
  Source field - The parsed JSON utilizing Postgres JSON parsing syntax formatted for storage in the database.
  Target field - The field to be added to the appropriate database table and where the parsed JSON data from the source field will be stored
  
  Target data type - The data type of the target field which will be added to the appropriate database table.
  Staging flag - Indicates whether the new field should be utilized for staging the manifest data. The value should always be false at this time.

The snippet that follows is an example of how to call the function to add a column:

```sql
SELECT manifest.fn_add_column(
  'DestinyActivityBundleDefinition',
  'json->>''bundleHash''',
  'bundle_hash',
  'BIGINT',
  FALSE
);
```
The function will output JSON as follows:

```json
{
  "alter_sql": "ALTER TABLE manifest.t_activity_bundle ADD bundle_hash BIGINT",
  "field_mapping": 1,
  "update_sql": "UPDATE manifest.t_activity_bundle SET bundle_hash = (json->>'bundleHash')::BIGINT",
  "update": 312
}
```

alter_sql - Shows the alter table statement executed against the appropriate table
field_mapping - Shows the count of records added to t_field_mappings
update_sql - Shows the update statement executed against the appropriate table to populate the new column
update - Shows the count of records in the appropriate table that were updated
