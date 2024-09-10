CREATE table if not exists "Customers" 
(
    "ID" INTEGER PRIMARY KEY,
    "Name" VARCHAR(255)
);

CREATE table if not exists "Events"
(
    "ID" integer primary key,
    "Name" varchar(255)
);

INSERT INTO Customers (name) VALUES('Bud Powell');

SELECT * FROM Customers LIMIT 5;
