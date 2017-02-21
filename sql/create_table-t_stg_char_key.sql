DROP TABLE IF EXISTS manifest.t_stg_char_key;

CREATE TABLE manifest.t_stg_char_key (
	key varchar(100) NOT NULL,
	json text,
	CONSTRAINT pk_stg_char_key PRIMARY KEY (key)
);