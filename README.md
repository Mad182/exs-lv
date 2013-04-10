exs-lv
======

Exs.lv dev


### mysql imports:

mysql -u exs -p exs < schema.sql
mysql -u exs -p exs < cat.sql
mysql -u exs -p exs < blacklisted_sites.sql


### mysql/smtp/memcache konfigurācija:

~/exs-lv/exs.lv/configdb.php
obligāti jānorāda mysql, memcache konfigs un ceļi:
CORE_PATH uz exs.lv folderi, 
LIB_PATH uz libs folderi.


### kas vajadzīgs, lai viss darbotos:
php-gd
php-memcache
memcached
imagemagick
mysql
apache2 ar mod_rewrite un .htaccess atbalstu

uz servera ir arī apc, bet darbību tā trūkumam teorētiski nevajadzētu ietekmēt.


nemainot site_loader.php strādās tikai uz adreses localhost, dev.exs.lv vai dzīvajām adresēm exs.lv/coding.lv utt., 
ip adreses un viss pārējais tiek redirektēts uz exs.lv
es ieteiktu uztaisīt virtualhostu, un hosts failā norādīt 127.0.0.1 dev.exs.lv


libs mapē vajadzīgi:
(instalējas kā submoduļi)

https://github.com/ezyang/htmlpurifier

https://github.com/swiftmailer/swiftmailer

https://github.com/Austinb/GameQ

principā ja negribas sūtīt meilus un nesatrauc serveru monitori, pietiek ar htmlpurifier.

## Arch

    sudo pacman -Su apache php php-apache php-gd php-memcache mariadb imagemagick memcached
    cd
    mkdir projects && cd projects
    git clone git@github.com:Mad182/exs-lv.git exs && cd exs
    git submodule init && git submodule update

Turpināt ar db/Apache2 iestatījumiem (vienkāršākais - LIB_PATH, CORE_PATH iekš httpd.conf ar SetEnv)
