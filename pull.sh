#!/bin/sh

sudo -u exs git pull
sudo service apache2 restart
sudo service varnish restart
