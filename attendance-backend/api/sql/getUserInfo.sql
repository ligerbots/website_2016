SELECT
	-- This gets the total time the user has been signed in
	(
		-- This replaces 'NULL' with '0' in the case the user has never signed in
		SELECT
			IFNULL
			(
				(
					-- This calculates the total time the user has been signed in
					SELECT
						SUM(calendar.end - calendar.start)
					FROM calendar
					WHERE
						calendar.end <> 0 AND
						NOT calendar.meta & b'00000001' AND -- This excludes events that are marked as "suspended"
						calendar.user = ?
				)
			-- In the event the above statement is 'NULL', this sets the value to use instead
			,0)
	) AS 'time',
	-- This gets the total time the user has been signed in
	(
		-- This replaces 'NULL' with '0' in the case the user has never signed in
		SELECT
			IFNULL
			(
				(
					-- This calculates the total time the user has been signed in
					SELECT
						SUM(calendar.end - calendar.start)
					FROM calendar
					WHERE
						calendar.end <> 0 AND
						NOT calendar.meta & b'00000001' AND -- This excludes events that are marked as "suspended"
						calendar.user = ? AND
						calendar.start > 1483765200 -- TODO: This hardcodes the 2017 kickoff date
				)
			-- In the event the above statement is 'NULL', this sets the value to use instead
			,0)
	) AS 'buildtime',
	-- This gets the total time the user has been present, including that which has been marked as suspended
	(
		-- This replaces 'NULL' with '0' in the case the user has never signed in
		SELECT
			IFNULL
			(
				(
					-- This calculates the total time the user has been signed in
					SELECT
						SUM(calendar.end - calendar.start)
					FROM calendar
					WHERE
						calendar.end <> 0 AND
						calendar.user = ?
				)
			-- In the event the above statement is 'NULL', this sets the value to use instead
			,0)
	) AS 'abstime',
	-- This gets weather or not the user is currently signed in
	(
		SELECT
			-- This checks if the following query returns anything
			CASE WHEN EXISTS
				(
					-- This returns any open events the user may have in their name
					SELECT
						calendar.end
					FROM calendar
					WHERE
						calendar.end = 0 AND
						NOT calendar.meta & b'00000001' AND	-- This excludes events that are marked as "suspended"
						calendar.user = ?
				)
				-- Return 1 if the statement is true (the user is signed in)
				THEN '1'
				-- Return 0 if the statement is false (the user is not signed in)
				ELSE '0'
			END
	) AS 'signedin'