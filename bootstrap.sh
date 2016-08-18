#!/bin/bash

sudo apt-get update
sudo apt-get install -y apache2 php5 php5-mysql curl php5-curl

curl -s https://getcomposer.org/installer | php
sudo mv composer.phar /usr/bin/composer

sudo a2enmod rewrite
sudo service apache2 restart


## MariaDB Database
sudo sudo sh -c 'echo "mysql-server mysql-server/root_password password root
mysql-server mysql-server/root_password_again password root" > /root/src/debconf.txt'
debconf-set-selections /root/src/debconf.txt

echo "mysql-server-5.5 mysql-server/root_password password root" > mysql_password.cfg
echo "mysql-server-5.5 mysql-server/root_password_again password root" >> mysql_password.cfg
debconf-set-selections mysql_password.cfg
sudo apt-get install -qq mariadb-server-5.5 mariadb-client

echo "CREATE DATABASE IF NOT EXISTS hikes" | mysql -u root -proot
echo "use hikes; source /vagrant/database/schema.sql;" | mysql -u root -proot

rm /var/www/html/index.html
