#!/bin/bash

#set timezone
echo "Europe/Paris" > /etc/timezone
dpkg-reconfigure --frontend noninteractive tzdata

# PHP
apt-get update && apt-get upgrade
apt-get install -y php5 php5-curl php5-imagick php5-mysql apache2

# Git
apt-get install -y git

# Python
apt-get install -y python-dev python-setuptools libjpeg8 libjpeg62-dev libfreetype6 libfreetype6-dev

# Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# MySQL
debconf-set-selections <<< 'mysql-server mysql-server/root_password password vagrant'
debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password vagrant'
apt-get -y install mysql-server

# Accept MySql connections from outside
sed -i 's/bind-address/#bind-address = 0.0.0.0 #/g' /etc/mysql/my.cnf
echo "bind-address 0.0.0.0" >> /etc/mysql/my.cnf
mysql -u root -pvagrant -e "GRANT ALL PRIVILEGES  on *.* to root@'%' IDENTIFIED BY 'vagrant'; FLUSH PRIVILEGES;"
service mysql restart

# Applying agentfowarding
echo -e "Host *\n    ForwardAgent yes" > /home/vagrant/.ssh/config

# Set environment variables needed
echo "SetEnv APPLICATION_ENV 'dev'" > /etc/apache2/conf-available/vagrant.conf
ln -s /etc/apache2/conf-available/vagrant.conf /etc/apache2/conf-enabled/vagrant.conf

cd /home/vagrant/
git clone https://github.com/JuanPotato/Legofy.git
cd Legofy
python setup.py install

cd /var/www/
mkdir .python-eggs
chown www-data:www-data -R .python-eggs

# Apache conf
VM_VHOST="
<VirtualHost *:80>
       SetEnv APPLICATION_ENV dev
       ServerName localhost
       DocumentRoot /vagrant/web
       <Directory /vagrant/web>
               Options All
               AllowOverride All
               Require all granted
       </Directory>
</VirtualHost>
"

VM_CONF_FILE="/etc/apache2/sites-available/legofy.conf"

[[ -f "$VM_CONF_FILE" ]] || touch "$VM_CONF_FILE"
[[ ! -f "$VM_CONF_FILE" ]] || echo "$VM_VHOST" > "$VM_CONF_FILE"

a2enmod rewrite
a2ensite legofy.conf
a2dissite 000-default.conf
service apache2 reload

