-- Groups
SELECT * 
FROM Groups

-- PartOfActor
-- arg (groupNm)
SELECT StageName
FROM PartOfActor
WHERE GroupName = groupNm

-- Visitor
-- arg (ticketNum)
SELECT VisitorName 
FROM Visitor
WHERE TicketNumber = ticketNum

-- Staff
-- arg (ID)
SELECT StaffName
FROM Staff
WHERE StaffID = ID

-- Insert
-- arg(rname, capacity)
INSERT INTO RESTAURANT
VALUES (rname, capacity)

-- Delete
-- arg(rname)
DELETE FROM RESTAURANT
WHERE RESTAURANTNAME = rname

-- Show the RESTAURANT and PROVIDES_ALCOHOLICDRINK Tables
SELECT *
FROM RESTAURANT

SELECT Count(*)
FROM PROVIDES_ALCOHOLICDRINK

SELECT *
FROM PROVIDES_ALCOHOLICDRINK

-- Projection
-- arg(column1, column2, …)
SELECT column1, column2, …
FROM PERFORMS_SHOW_R2

-- Having
-- arg(minShows)
SELECT GENRE, COUNT(*)
FROM PERFORMS_SHOW_R1 r1, PERFORMS_SHOW_R2 r2
WHERE r1.TITLE = r2.TITLE
GROUP BY GENRE
HAVING COUNT(*) >= minShows 

-- Display the Show Schedule
SELECT STARTTIME, r1.TITLE, GENRE, SEATS, GROUPNAME
FROM PERFORMS_SHOW_R1 r1, PERFORMS_SHOW_R2 r2
WHERE r1.TITLE = r2.TITLE
ORDER BY STARTTIME


