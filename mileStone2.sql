CREATE TABLE Groups (GroupName char(50) PRIMARY KEY);
INSERT INTO Groups VALUES ('Happy Group');
INSERT INTO Groups VALUES ('Avengers');
INSERT INTO Groups VALUES ('Transformers');
INSERT INTO Groups VALUES ('Guardians');
INSERT INTO Groups VALUES ('Minions');

CREATE TABLE PartOfActor(StageName char(50) PRIMARY KEY, GroupName char(50) NOT NULL, FOREIGN KEY (GroupName)
REFERENCES Groups);
INSERT INTO PartOfActor VALUES ('Bob', 'Happy Group');
INSERT INTO PartOfActor VALUES ('Sara', 'Happy Group');
INSERT INTO PartOfActor VALUES ('Steve Rogers', 'Avengers');
INSERT INTO PartOfActor VALUES ('Loki', 'Avengers');
INSERT INTO PartOfActor VALUES ('Rocket', 'Guardians');
INSERT INTO PartOfActor VALUES ('Groot', 'Guardians');
INSERT INTO PartOfActor VALUES ('Kevin', 'Minions');
INSERT INTO PartOfActor VALUES ('Stuart', 'Minions');

CREATE TABLE Performs_Show_R1 (Title char(50) PRIMARY KEY, Genre char(50));
CREATE TABLE Performs_Show_R2 (StartTime integer, Seats integer, Title char(50), GroupName char(50), PRIMARY KEY (StartTime, Title), FOREIGN KEY (GroupName) REFERENCES Groups);

INSERT INTO Performs_Show_R1 VALUES('The Happy Show', 'Action');
INSERT INTO Performs_Show_R1 VALUES('The Happy Show Continued', 'Comedy');
INSERT INTO Performs_Show_R1 VALUES('Lokis Adventures', 'Action');
INSERT INTO Performs_Show_R1 VALUES('Groot Growing Up', 'Action');
INSERT INTO Performs_Show_R1 VALUES('Steves Sorrows', 'Tragedy');
INSERT INTO Performs_Show_R1 VALUES('Minions 1', 'Comedy');
INSERT INTO Performs_Show_R1 VALUES('Minions 2', 'Comedy');

INSERT INTO Performs_Show_R2 VALUES(0800, 200, 'Lokis Adventures', 'Avengers');
INSERT INTO Performs_Show_R2 VALUES(0900, 200, 'Lokis Adventures', 'Avengers');
INSERT INTO Performs_Show_R2 VALUES(1100, 100, 'Steves Sorrows', 'Avengers');
INSERT INTO Performs_Show_R2 VALUES(1200, 100, 'The Happy Show', 'Happy Group');
INSERT INTO Performs_Show_R2 VALUES(1400, 100, 'The Happy Show Continued', 'Happy Group');
INSERT INTO Performs_Show_R2 VALUES(1300, 200, 'Groot Growing Up', 'Guardians');
INSERT INTO Performs_Show_R2 VALUES (0900, 100, 'Minions 1', 'Minions');
INSERT INTO Performs_Show_R2 VALUES (1100, 100, 'Minions 2', 'Minions');

CREATE TABLE Visitor (TicketNumber Integer PRIMARY KEY, VisitorName char(50));
INSERT INTO Visitor VALUES (10001, 'Bob Jones');
INSERT INTO Visitor VALUES (10002, 'Maria Jones');
INSERT INTO Visitor VALUES (10003, 'Bob Jr Jones');
INSERT INTO Visitor VALUES (10004, 'Mary Sue');
INSERT INTO Visitor VALUES (10005, 'Ken Sue');
INSERT INTO Visitor VALUES (10006, 'Barbie Alex');
INSERT INTO Visitor VALUES (10007, 'Jenna Sue');
INSERT INTO Visitor VALUES (10008, 'Hailey Wu');
INSERT INTO Visitor VALUES (10009, 'Madeline Dow');
INSERT INTO Visitor VALUES (10010, 'Sophia Zhou');

CREATE TABLE Watches(StartTime Integer, Title char(50), TicketNumber Integer, 
FOREIGN KEY (StartTime, Title) REFERENCES Performs_Show_R2, FOREIGN KEY (TicketNumber) REFERENCES Visitor, 
PRIMARY KEY (StartTime, Title, TicketNumber));
INSERT INTO Watches VALUES (1200, 'The Happy Show', 10001);
INSERT INTO Watches VALUES (1200, 'The Happy Show', 10002);
INSERT INTO Watches VALUES (0800, 'Lokis Adventures', 10001);
INSERT INTO Watches VALUES (1100, 'Steves Sorrows', 10004);
INSERT INTO Watches VALUES (1100, 'Steves Sorrows', 10005);
INSERT INTO Watches VALUES (1300, 'Groot Growing Up', 10002);

CREATE TABLE Child (Height INTEGER, TicketNumber INTEGER PRIMARY KEY, FOREIGN KEY (TicketNumber) REFERENCES Visitor);
INSERT INTO Child VALUES (140, 10006);
INSERT INTO Child VALUES (130, 10007);
INSERT INTO Child VALUES (129, 10008);
INSERT INTO Child VALUES (131, 10009);
INSERT INTO Child VALUES (160, 10010);

CREATE TABLE Adult (Age INTEGER, TicketNumber INTEGER PRIMARY KEY, FOREIGN KEY (TicketNumber) REFERENCES Visitor);
INSERT INTO Adult VALUES (40, 10001);
INSERT INTO Adult VALUES (35, 10002);
INSERT INTO Adult VALUES (18, 10003);
INSERT INTO Adult VALUES (30, 10004);
INSERT INTO Adult VALUES (55, 10005);

CREATE TABLE Staff(StaffID Integer PRIMARY KEY, StaffName char(50));
INSERT INTO Staff VALUES(1, 'Anna');
INSERT INTO Staff VALUES(2, 'Ben');
INSERT INTO Staff VALUES (3, 'Charlie');
INSERT INTO Staff VALUES(4, 'Drew');
INSERT INTO Staff VALUES(5, 'Elsa');

Create Table Operates_Ride_R1 (RideType char(50) PRIMARY KEY, HeightRestriction integer); 
Create Table Operates_Ride_R2 (RideName char(50) PRIMARY KEY, Capacity integer, RideType char(50), StaffID integer, FOREIGN KEY (StaffID) REFERENCES Staff);

INSERT INTO Operates_Ride_R1 VALUES('Roller Coaster', 130);
INSERT INTO Operates_Ride_R1 VALUES('Drop', 120);
INSERT INTO Operates_Ride_R1 VALUES('Wheel', 0);
INSERT INTO Operates_Ride_R1 VALUES('Carousel', 0);
INSERT INTO Operates_Ride_R1 VALUES('Cars', 100);

INSERT INTO Operates_Ride_R2 VALUES('Splash Mountain', 6, 'Roller Coaster', 1);
INSERT INTO Operates_Ride_R2 VALUES('Tower of Terror', 24, 'Drop', 2);
INSERT INTO Operates_Ride_R2 VALUES('Ferris Wheel', 4, 'Wheel', 3);
INSERT INTO Operates_Ride_R2 VALUES('Happy Carousel', 30, 'Carousel', 4);
INSERT INTO Operates_Ride_R2 VALUES('Bumper Cars', 12, 'Cars', 4);

CREATE TABLE GoesOn(RideName char(50), TicketNumber Integer, 
PRIMARY KEY (RideName, TicketNumber), 
FOREIGN KEY (RideName) REFERENCES Operates_Ride_R2, 
FOREIGN KEY (TicketNumber) REFERENCES Visitor);
INSERT INTO GoesOn VALUES ('Splash Mountain', 10001);
INSERT INTO GoesOn VALUES ('Splash Mountain', 10002);
INSERT INTO GoesOn VALUES ('Tower of Terror', 10001);
INSERT INTO GoesOn VALUES ('Happy Carousel', 10006);
INSERT INTO GoesOn VALUES ('Bumper Cars', 10005);
INSERT INTO GoesOn VALUES ('Ferris Wheel', 10001);
INSERT INTO GoesOn VALUES ('Happy Carousel', 10001);
INSERT INTO GoesOn VALUES ('Bumper Cars', 10001);


CREATE TABLE Restaurant(RestaurantName char(50) PRIMARY KEY, Capacity Integer);
INSERT INTO Restaurant VALUES('Princess Tea Party', 50);
INSERT INTO Restaurant VALUES('Death Eater Bar', 20);
INSERT INTO Restaurant VALUES('Marios Buffet', 100);
INSERT INTO Restaurant VALUES('Asgardian Feast', 100);
INSERT INTO Restaurant VALUES('Bobs Burgers', 50);

CREATE TABLE Provides_AlcoholicDrink(RestaurantName char(50), DrinkName char(50), Price Integer, 
PRIMARY KEY (RestaurantName, DrinkName),
FOREIGN KEY (RestaurantName) REFERENCES Restaurant
ON DELETE CASCADE);
INSERT INTO Provides_AlcoholicDrink VALUES('Death Eater Bar', 'Avada Kevodka', 6.99);
INSERT INTO Provides_AlcoholicDrink VALUES('Death Eater Bar', 'Thunder Beer', 10.99);
INSERT INTO Provides_AlcoholicDrink VALUES('Asgardian Feast', 'Thunder Beer', 5.99);
INSERT INTO Provides_AlcoholicDrink VALUES('Marios Buffet', 'Super Margarita', 10.99);
INSERT INTO Provides_AlcoholicDrink VALUES('Death Eater Bar', 'Winegardian Leviosa', 6.99);

CREATE TABLE DinesAt (TicketNumber INTEGER, RestaurantName char(50), PRIMARY KEY (TicketNumber, RestaurantName),
FOREIGN KEY (TicketNumber) REFERENCES Visitor, FOREIGN KEY (RestaurantName) REFERENCES Restaurant ON DELETE CASCADE);
INSERT INTO DinesAt VALUES (10001, 'Princess Tea Party');
INSERT INTO DinesAt VALUES (10001, 'Death Eater Bar');
INSERT INTO DinesAt VALUES (10002, 'Death Eater Bar');
INSERT INTO DinesAt VALUES (10003, 'Marios Buffet');
INSERT INTO DinesAt VALUES (10004, 'Asgardian Feast');

CREATE TABLE Purchases (TicketNumber Integer, 
                        RestaurantName char(50), 
                        DrinkName char(50), 
                        PRIMARY KEY (TicketNumber, RestaurantName, DrinkName), 
                        FOREIGN KEY (TicketNumber) REFERENCES Adult, 
                        FOREIGN KEY (RestaurantName, DrinkName) REFERENCES Provides_AlcoholicDrink ON DELETE CASCADE);
INSERT INTO Purchases VALUES(10001, 'Death Eater Bar', 'Thunder Beer');
INSERT INTO Purchases VALUES(10004, 'Asgardian Feast', 'Thunder Beer');
INSERT INTO Purchases VALUES(10004, 'Death Eater Bar', 'Thunder Beer');
INSERT INTO Purchases VALUES(10003, 'Marios Buffet', 'Super Margarita');
INSERT INTO Purchases VALUES(10002, 'Death Eater Bar', 'Avada Kevodka');

