DROP TABLE if exists Users;

CREATE TABLE if not exists "Users" 
(
    "ID" INTEGER PRIMARY KEY,
    "name" VARCHAR(255),
    "password" varchar(255)
);

CREATE TABLE if not exists "Events"
(
    "ID" integer primary key,
    "name" varchar(255)
);

INSERT INTO Users (ID, name, password) VALUES(1, 'Bud Powell', '12345');

SELECT * FROM Users WHERE ID = 1;

UPDATE Users SET password = "abc" WHERE ID = 1;

SELECT * FROM Users WHERE ID = 1;