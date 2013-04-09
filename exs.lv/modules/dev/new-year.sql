UPDATE users SET year_first = 1 WHERE id IN(SELECT DISTINCT(author) FROM `miniblog` WHERE `date` LIKE '%-01-01 00:00:%')
