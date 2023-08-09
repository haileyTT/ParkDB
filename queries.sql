-- Selection
-- args (c1, c2)
SELECT RestaurantName
FROM Restaurant
WHERE Capacity > c1 AND Capacity < c2

-- Aggregation with group by
SELECT RestaurantName, MIN(Price)
FROM Provides_AlcoholicDrink
GROUP BY RestaurantName;

-- Division
-- Find visitors who have gone to all rides
SELECT VisitorName
FROM Visitor V
WHERE NOT EXISTS ((SELECT R.RideName
                FROM Operates_Ride_R2 R) 
                MINUS
                (SELECT S.RideName
                FROM GoesOn S            
                WHERE S.TicketNumber = V.TicketNumber));


-- Insert
-- arg(rname, capacity)
INSERT INTO RESTAURANT
VALUES (rname, capacity);

-- Delete
-- arg(rname)
DELETE FROM RESTAURANT
WHERE RESTAURANTNAME = rname;

-- Show the RESTAURANT and PROVIDES_ALCOHOLICDRINK Tables
SELECT *
FROM RESTAURANT;

SELECT Count(*)
FROM PROVIDES_ALCOHOLICDRINK;

SELECT *
FROM PROVIDES_ALCOHOLICDRINK;

-- Projection
-- arg(column1, column2, …)
SELECT column1, column2, …
FROM PERFORMS_SHOW_R2;

-- Having
-- arg(minShows)
SELECT GENRE, COUNT(*)
FROM PERFORMS_SHOW_R1 r1, PERFORMS_SHOW_R2 r2
WHERE r1.TITLE = r2.TITLE
GROUP BY GENRE
HAVING COUNT(*) >= minShows;

-- Display the Show Schedule
SELECT STARTTIME, r1.TITLE, GENRE, SEATS, GROUPNAME
FROM PERFORMS_SHOW_R1 r1, PERFORMS_SHOW_R2 r2
WHERE r1.TITLE = r2.TITLE
ORDER BY STARTTIME;



--Find the types of rides with capacity thats greater than the average capacity of all the ride types 


SELECT RideType
FROM Operates_Ride_R2 r2
GROUP BY RideType
HAVING avg(Capacity) > (SELECT avg(Capacity)
                        FROM Operates_Ride_R2);

-- Find the name of all visitors who have been on a ride (Join)
SELECT VisitorName
FROM Visitor v, GoesOn g
WHERE v.TicketNumber = g.TicketNumber;


-- Update RideName


UPDATE Operates_Ride_R2
SET RideName = 'Splasher'
WHERE RideName = 'Splash Mountain';
