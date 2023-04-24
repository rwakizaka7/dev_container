LOAD DATA LOCAL INFILE '/docker-entrypoint-initdb.d/3_test1_table_import.csv'
INTO TABLE TEST1_TABLE FIELDS TERMINATED BY ',' ENCLOSED BY '"'