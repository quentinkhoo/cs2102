CREATE TABLE person(
	username VARCHAR(20) UNIQUE NOT NULL,
	password VARCHAR(256) NOT NULL,
	email VARCHAR(100) UNIQUE NOT NULL,
	fullname VARCHAR(256) NOT NULL,
	phone NUMERIC UNIQUE NOT NULL,
	userid SERIAL PRIMARY KEY
);

CREATE TYPE seat AS ENUM('2-seater', '5-seater', '7-seater');
CREATE TABLE car(
	model VARCHAR(100) NOT NULL,
	colour VARCHAR(50) NOT NULL,
	seats seat NOT NULL,
	license VARCHAR(10) UNIQUE NOT NULL,
	carid SERIAL PRIMARY KEY
);

CREATE TABLE car_ownedby (
	ownerid INTEGER,
	carid INTEGER,
	FOREIGN KEY(ownerid) REFERENCES person(userid),
	FOREIGN KEY(carid) REFERENCES car(carid),
	PRIMARY KEY(ownerid, carid)
);

CREATE TYPE status AS ENUM('open','close');
CREATE TABLE ride (
	origin VARCHAR(256) NOT NULL,
	dest VARCHAR(256) NOT NULL,
	pickuptime TIMESTAMP NOT NULL,
	minbid MONEY DEFAULT 0.00,
	status status NOT NULL,
	carid INTEGER NOT NULL,
	advertiserid INTEGER NOT NULL,
	FOREIGN KEY (advertiserid) REFERENCES person(userid),
	FOREIGN KEY (carid) REFERENCES car(carid),
	PRIMARY KEY (origin,dest,pickuptime,advertiserid)
);	

CREATE TABLE bid(
	bidamt MONEY DEFAULT 0.05,
	bidtime TIMESTAMP,
	bidderid INTEGER NOT NULL,
	advertiserid INTEGER NOT NULL,
	origin VARCHAR(256) NOT NULL,
	dest VARCHAR(256) NOT NULL,
	pickuptime TIMESTAMP NOT NULL,
	minbid MONEY NOT NULL,
	FOREIGN KEY (bidderid) REFERENCES person(userid),
	FOREIGN KEY (advertiserid, origin, dest, pickuptime) REFERENCES ride(advertiserid, origin, dest, pickuptime),
	PRIMARY KEY(bidderid, advertiserid, origin, dest, pickuptime)
);