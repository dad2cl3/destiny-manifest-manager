DROP TABLE IF EXISTS manifest.t_stg_int_key;

CREATE TABLE manifest.t_stg_int_key (
	id int8 NOT NULL,
	json text,
	CONSTRAINT t_stg_int_key_pkey PRIMARY KEY (id)
);