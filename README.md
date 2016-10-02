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
* php-memcache
* php-curl
* memcached
* mysql
* apache2 vai nginx

### php7-fpm un vajadzīgo moduļu uzstādīšana uz debian/ubuntu

    apt-get install php7.0-fpm php7.0-memcached php7.0-gd php7.0-xml php7.0-mbstring php7.0-mcrypt php7.0-curl

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

    /opt/certbot-auto certonly --webroot -w /home/www/exs.lv -d exs.lv -d www.exs.lv -d coding.lv -d www.coding.lv -d lol.exs.lv -d rp.exs.lv -d rs.exs.lv -d runescape.exs.lv -d secure.exs.lv -d static.exs.lv -w /home/www/m.exs.lv -d m.coding.lv -d m.exs.lv -d mlol.exs.lv -d mrp.exs.lv -d mrs.exs.lv -d mrunescape.exs.lv -w /home/www/api.exs.lv -d api.exs.lv -d android.exs.lv -d ios.exs.lv -w /var/www/munin -d munin.exs.lv -w /home/www/img.exs.lv -d img.exs.lv


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