exs-lv
======

Exs.lv dev


mysql imports:
~/exs-lv/dev-draza$ mysql -u exs -p exs < schema.sql
~/exs-lv/dev-draza$ mysql -u exs -p exs < cat.sql


mysql/smtp/memcache konfigurācija:
~/exs-lv/exs.lv/configdb.php

obligāti jānorāda mysql, memcache konfigs un ceļi:
CORE_PATH uz exs.lv folderi, 
LIB_PATH uz libs folderi.



kas vajadzīgs, lai viss darbotos:
php-gd, php-memcached, imagemagick, mysql
uz servera ir arī apc, bet darbību tā trūkumam teorētiski nevajadzētu ietekmēt.

nemainot site_loader.php strādās tikai uz adreses localhost, dev.exs.lv vai dzīvajām adresēm exs.lv/coding.lv utt., 
ip adreses un viss pārējais tiek redirektēts uz exs.lv
es ieteiktu uztaisīt virtualhostu, un hosts failā norādīt 127.0.0.1 dev.exs.lv


libs mapē vajadzīgi:
https://github.com/ezyang/htmlpurifier
https://github.com/swiftmailer/swiftmailer
https://github.com/Austinb/GameQ
principā ja negribas sūtīt meilus un nesatrauc serveru monitori, pietiek ar htmlpurifier.
