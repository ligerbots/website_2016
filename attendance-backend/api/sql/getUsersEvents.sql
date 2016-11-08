SELECT
	-- Basic event information
	calendar.id,
	calendar.user,
	calendar.start,
	calendar.end,
	ORD(calendar.meta) AS 'meta',
	-- Checks if the event is open
	IF(calendar.end = 0, 1, 0) AS 'isopen'
FROM calendar
WHERE user=?
ORDER BY calendar.start ASC