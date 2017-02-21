DROP TABLE IF EXISTS manifest.t_stg_int_key;

CREATE TABLE manifest.t_stg_int_key (
	id int8 NOT NULL,
	json text,
	CONSTRAINT pk_stg_int_id PRIMARY KEY (id)
);