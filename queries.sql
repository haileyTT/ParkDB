-- Groups
SELECT * 
FROM Groups

SELECT COUNT(*)
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

