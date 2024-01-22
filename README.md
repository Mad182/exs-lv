## Uzstādīšana ##

### lejupielādē sources ###

    git clone git@bitbucket.org:mad182/exs-lv.git && cd exs-lv
    git submodule init && git submodule update

### mysql imports: ###

    mysql -u exs -p exs < dev-draza/schema.sql
    mysql -u exs -p exs < dev-draza/cat.sql


### mysql/smtp/memcache konfigurācija:

    cp exs.lv/configdb.php.sample exs.lv/configdb.php

Konfigurācijas failā obligāti jānorāda mysql, memcache konfigs un absolūtie ceļi uz failiem:

* CORE_PATH uz exs.lv folderi
* LIB_PATH uz libs folderi


### Kas vajadzīgs, lai lapa būtu palaižama ###

* php-gd
* php-memcached
* php-curl
* php-mbstring
* php-mcrypt
* php-xml (uz php 7)
* memcached
* mysql
* apache2 vai nginx

### php7-fpm un vajadzīgo moduļu uzstādīšana uz debian/ubuntu

    apt install php8.3-cli php8.3-common php8.3-curl php8.3-fpm php8.3-gd php8.3-intl php8.3-mbstring php8.3-mysql php8.3-opcache php8.3-readline php8.3-xml php8.3-zip

### Memcached uz servera ar Windows OS

Lejuplādē servisu no: http://downloads.northscale.com/memcached-win64-1.4.4-14.zip

    pthreadGC2.dll -> C:\Windows\System32\
    memcached.exe -> C:\memcached\

Palaid memcached.exe servisu:

    memcached.exe -d install
    memcached.exe -d start

Lejuplādē "Memcache" PHP 7.0 extension:

    https://github.com/nono303/PHP7-memcahe-dll/blob/master/vc14/x86/ts/php_memcache.dll
    php_memcache.dll -> C:\xampp\php\ext\

Palaid "Memcache":

    * C:\xampp\php\php.ini -> iekopē rindu: extension=php_memcache.dll
    * iedarbini Apache
    * C:\www\exs-lv\exs.lv\configdb.php -> atkomentē ar Memcache saistīto klasi

Pamācība:

    https://commaster.net/content/installing-memcached-windows

### Lai pilnvērtīgi darbotos img.exs.lv ###

* imagemagick
* advancecomp
* pngcrush
* optipng
* jpegoptim

Uz servera ir arī apc, bet darbību tā trūkumam nevajadzētu ietekmēt

Nemainot site_loader.php strādās tikai uz adreses localhost, dev.exs.lv vai dzīvajām adresēm exs.lv/coding.lv utt.,
ip adreses un viss pārējais tiek redirektēts uz exs.lv

### Let's encrypt ###

    certbot certonly --webroot -w /home/www/exs.lv -d exs.lv -d www.exs.lv -d coding.lv -d www.coding.lv -d lol.exs.lv -d rs.exs.lv -d runescape.exs.lv -w /home/www/m.exs.lv -d m.coding.lv -d m.exs.lv -d mlol.exs.lv -d mrs.exs.lv -w /home/www/api.exs.lv -d api.exs.lv -d android.exs.lv -w /var/www/munin -d munin.exs.lv -w /home/www/img.exs.lv -d img.exs.lv -w /var/www/gif-avatars.com/app/webroot -d gif-avatars.com -d www.gif-avatars.com

## Nepieciešamo programmu uzstādīšana ##

### Arch ###

    sudo pacman -Su apache php php-apache php-gd php-memcache mariadb imagemagick memcached


### PHP/MySQL uzstādīšana uz OSX ###

Install homebrew

    ruby -e "$(curl -fsSL https://raw.github.com/Homebrew/homebrew/go/install)"
 
Install mysql

    brew install mariadb
    unset TMPDIR
    mysql_install_db --user=`whoami` --basedir="$(brew --prefix mariadb)" --datadir=/usr/local/var/mysql --tmpdir=/tmp
    ln -sfv /usr/local/opt/mariadb/*.plist ~/Library/LaunchAgents
    mysql.server start

Install php

    brew tap homebrew/dupes
    brew tap josegonzalez/homebrew-php
    brew install php54

Install memcached

    brew install memcached
    brew install libmemcached
    brew install php54-memcache

add 

    extension=/usr/local/Cellar/php55-memcache/2.2.7/memcached.so
to /etc/php.ini

add

    LoadModule php5_module /usr/local/Cellar/php54/5.4.23/libexec/apache2/libphp5.so
to /etc/apache2/httpd.conf
