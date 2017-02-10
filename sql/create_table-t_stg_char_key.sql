DROP TABLE IF EXISTS manifest.t_stg_char_key;

CREATE TABLE manifest.t_stg_char_key (
	key varchar(100) NOT NULL,
	json text,
	CONSTRAINT t_stg_char_key_pkey PRIMARY KEY (key)
);