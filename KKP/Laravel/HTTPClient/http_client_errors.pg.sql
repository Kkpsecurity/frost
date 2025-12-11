SET ROLE DBNAME;

--
--
--


DROP TABLE IF EXISTS http_client_errors;

CREATE TABLE http_client_errors
(

	id 				bigserial,
	created_at		timestamptz			NOT NULL DEFAULT now(),

	facility		varchar(255)		NOT NULL,
	action			varchar(255)		NOT NULL,
	user_id			bigint,

	-- request --

	request_method	varchar(16)			NOT NULL, -- GET, POST, PATCH
	url				varchar(255)		NOT NULL,
	form_params		json,

	-- response --

	http_code		integer				NOT NULL,
	response_json	json,
	response_text	text,
	curl_error		text

);


CREATE INDEX http_client_errors_created_at_idx	ON http_client_errors ( created_at	);
CREATE INDEX http_client_errors_facility_idx	ON http_client_errors ( facility	);
CREATE INDEX http_client_errors_action_idx		ON http_client_errors ( action		);
CREATE INDEX http_client_errors_user_id_idx		ON http_client_errors ( user_id		);
CREATE INDEX http_client_errors_http_code_idx	ON http_client_errors ( http_code	);


ALTER TABLE http_client_errors
	ADD CONSTRAINT http_client_errors_user_id_fkey	FOREIGN KEY ( user_id ) REFERENCES users ( id ),
	ADD CONSTRAINT http_client_errors_id_fkey		PRIMARY KEY ( id );
