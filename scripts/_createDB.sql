-- Create Database Template
-- Usage: sed "s/DBNAME/your_database_name/g" _createDB.sql | psql template1

-- Drop database if exists
DROP DATABASE IF EXISTS DBNAME;

-- Create fresh database
CREATE DATABASE DBNAME
    WITH 
    OWNER = postgres
    ENCODING = 'UTF8'
    LC_COLLATE = 'en_US.UTF-8'
    LC_CTYPE = 'en_US.UTF-8'
    TEMPLATE = template0
    CONNECTION LIMIT = -1;

-- Grant privileges
GRANT ALL PRIVILEGES ON DATABASE DBNAME TO postgres;
GRANT CONNECT ON DATABASE DBNAME TO frost;
