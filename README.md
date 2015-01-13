## Uzstādīšana ##

### lejupielādē srouces ###

    git clone git@bitbucket.org:mad182/exs-lv.git && cd exs-lv
    git submodule init && git submodule update

### mysql imports: ###

    mysql -u exs -p exs < schema.sql
    mysql -u exs -p exs < cat.sql


### mysql/smtp/memcache konfigurācija:

~/exs-lv/exs.lv/configdb.php
obligāti jānorāda mysql, memcache konfigs un ceļi:
* CORE_PATH uz exs.lv folderi, 
* LIB_PATH uz libs folderi.


### Kas vajadzīgs, lai lapa būtu palaižama ###

* php-gd
* php-memcache
* php-curl
* memcached
* mysql
* apache2 ar mod_rewrite un .htaccess atbalstu

### Lai pilnvērtīgi darbotos img.exs.lv ###

* imagemagick
* advancecomp
* pngcrush
* optipng
* jpegoptim

Uz servera ir arī apc, bet darbību tā trūkumam nevajadzētu ietekmēt

Nemainot site_loader.php strādās tikai uz adreses localhost, dev.exs.lv vai dzīvajām adresēm exs.lv/coding.lv utt.,
ip adreses un viss pārējais tiek redirektēts uz exs.lv


## Arch ##

    sudo pacman -Su apache php php-apache php-gd php-memcache mariadb imagemagick memcached
    cd
    mkdir projects && cd projects

Turpināt ar db/Apache2 iestatījumiem (vienkāršākais - LIB_PATH, CORE_PATH iekš httpd.conf ar SetEnv)


## OSX
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

    extension=/usr/local/Cellar/php54-memcached/2.1.0/memcached.so
to /etc/php.ini

add

    LoadModule php5_module /usr/local/Cellar/php54/5.4.23/libexec/apache2/libphp5.so
to /etc/apache2/httpd.conf