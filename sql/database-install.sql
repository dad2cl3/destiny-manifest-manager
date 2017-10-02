\i create_database.sql
\c YOUR-DATABASE 
/* database roles and users should be created prior to creating any additional database objects */

\i create_schema-manifest.sql
\i create_sequence-seq_t_manifest_version_manifest_id.sql
\i create_trigger_function-fn_last_updated.sql

/* Primary key manifest_id will be referenced by foreign keys in t_manifest */

\i create_table-t_manifest_version.sql
\i create_table-t_manifest.sql
\i create_table-t_manifest_stage.sql

\i create_function-fn_load_manifest.sql

/* Add additional script that includes all necessary grants to roles/users */
