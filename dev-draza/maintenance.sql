##fix TinyMCE broken links inside [spoiler] tags
UPDATE  `miniblog` SET  `text` = REPLACE(  `text` ,  '%5B/spoiler%5D',  '' );
UPDATE `miniblog` SET `text` = REPLACE(`text`, '[/spoiler]</a>', '</a>[/spoiler]');
UPDATE  `pages` SET  `text` = REPLACE(  `text` ,  '%5B/spoiler%5D',  '' );
UPDATE `pages` SET `text` = REPLACE(`text`, '[/spoiler]</a>', '</a>[/spoiler]');
UPDATE  `comments` SET  `text` = REPLACE(  `text` ,  '%5B/spoiler%5D',  '' );
UPDATE `comments` SET `text` = REPLACE(`text`, '[/spoiler]</a>', '</a>[/spoiler]');
UPDATE  `galcom` SET  `text` = REPLACE(  `text` ,  '%5B/spoiler%5D',  '' );
UPDATE `galcom` SET `text` = REPLACE(`text`, '[/spoiler]</a>', '</a>[/spoiler]');

##find references to deleted users
SELECT `clans_members`.* FROM `clans_members` left join `users` on `users`.`id` = `clans_members`.`user` WHERE `users`.`id` is null
SELECT `friends`.* FROM `friends` left join `users` on `users`.`id` = `friends`.`friend1` WHERE `users`.`id` is null
SELECT `friends`.* FROM `friends` left join `users` on `users`.`id` = `friends`.`friend2` WHERE `users`.`id` is null
SELECT `pm`.`id` FROM `pm` left join `users` on `users`.`id` = `pm`.`from_uid` WHERE `users`.`id` is null LIMIT 500
SELECT `pm`.`id` FROM `pm` left join `users` on `users`.`id` = `pm`.`to_uid` WHERE `users`.`id` is null LIMIT 500
SELECT `miniblog`.* FROM `miniblog` left join `users` on `users`.`id` = `miniblog`.`author` WHERE `users`.`id` is null AND `miniblog`.`posts` = 0 LIMIT 500
SELECT  `userlogs`.`id` FROM  `userlogs` LEFT JOIN  `users` ON  `users`.`id` =  `userlogs`.`user` WHERE  `users`.`id` IS NULL LIMIT 500

##very old and unconfirmed friendship requests
DELETE FROM `friends` WHERE `date` < '2013-01-01 00:00:00' AND `confirmed` = 0

