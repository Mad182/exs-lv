exs-lv
======

Exs.lv dev


### mysql imports:

mysql -u exs -p exs < schema.sql<br />
mysql -u exs -p exs < cat.sql


### mysql/smtp/memcache konfigurācija:

~/exs-lv/exs.lv/configdb.php
obligāti jānorāda mysql, memcache konfigs un ceļi:
CORE_PATH uz exs.lv folderi, 
LIB_PATH uz libs folderi.


### kas vajadzīgs, lai lapa būtu palaižama:
php-gd<br />
php-memcache<br />
php-curl<br />
memcached<br />
mysql<br />
apache2 ar mod_rewrite un .htaccess atbalstu

### lai darbotos img.exs.lv:
imagemagick<br />
advancecomp<br />
pngcrush<br />
optipng<br />
jpegoptim<br />
<br />
uz servera ir arī apc, bet darbību tā trūkumam nevajadzētu ietekmēt<br />
<br />
<br />
nemainot site_loader.php strādās tikai uz adreses localhost, dev.exs.lv vai dzīvajām adresēm exs.lv/coding.lv utt., <br />
ip adreses un viss pārējais tiek redirektēts uz exs.lv<br />
es ieteiktu uztaisīt virtualhostu, un hosts failā norādīt 127.0.0.1 dev.exs.lv<br />


libs mapē vajadzīgi:
(instalējas kā submoduļi)

https://github.com/ezyang/htmlpurifier<br />
https://github.com/swiftmailer/swiftmailer<br />
https://github.com/Austinb/GameQ<br />
https://github.com/facebook/facebook-php-sdk


## Arch

    sudo pacman -Su apache php php-apache php-gd php-memcache mariadb imagemagick memcached
    cd
    mkdir projects && cd projects
    git clone git@github.com:Mad182/exs-lv.git exs && cd exs
    git submodule init && git submodule update

Turpināt ar db/Apache2 iestatījumiem (vienkāršākais - LIB_PATH, CORE_PATH iekš httpd.conf ar SetEnv)
