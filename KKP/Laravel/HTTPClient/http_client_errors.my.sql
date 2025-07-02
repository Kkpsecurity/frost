--
--
--

DROP TABLE IF EXISTS http_client_errors;

CREATE TABLE http_client_errors
(

	id				INTEGER  UNSIGNED	NOT NULL AUTO_INCREMENT PRIMARY KEY,
	created_at		TIMESTAMP			NOT NULL DEFAULT CURRENT_TIMESTAMP(),

	facility		VARCHAR(255)		NOT NULL, -- TeachableAPI, Keymaster, PayPalPayFlow
	action			VARCHAR(255)		NOT NULL,
	user_id			INTEGER  UNSIGNED,

	-- request --

	request_method	VARCHAR(16)			NOT NULL, -- GET, POST, PATCH
	url				VARCHAR(255)		NOT NULL,
	form_params		JSON,

	-- response --

	http_code		INTEGER  UNSIGNED	NOT NULL,
	response_json	JSON,
	response_text	TEXT,
	curl_error		TEXT

);


CREATE INDEX http_client_errors_created_at_idx	ON http_client_errors ( created_at	);
CREATE INDEX http_client_errors_facility_idx	ON http_client_errors ( facility		);
CREATE INDEX http_client_errors_action_idx		ON http_client_errors ( action		);
CREATE INDEX http_client_errors_user_id_idx		ON http_client_errors ( user_id		);
CREATE INDEX http_client_errors_http_code_idx	ON http_client_errors ( http_code	);


ALTER TABLE http_client_errors
	ADD CONSTRAINT http_client_errors_user_id_fkey	FOREIGN KEY ( user_id ) REFERENCES users ( id );
