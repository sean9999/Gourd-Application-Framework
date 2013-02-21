# README

## About Gourd
Gourd is a PHP application framework.

## Core concepts

## Configuring your server

This quick-start makes the following assumptions:

1. You are a root-able user on a linux box (we'll assume Ubuntu but can be any flavour)
2. The box has [Apache](http://httpd.apache.org) running
3. You have access to a mongo db server, and mysql server (can be either [local](https://help.ubuntu.com/community/ApacheMySQLPHP) or a [mysql](http://aws.amazon.com/rds) or [mongo]((https://www.mongohq.com)) cloud provider)
4. `apt-get install php-pear, pecl`
5. `pecl install mongodb`

## Quick Start

1. Do everything in "Configuring your server"
2. clone this repo
3. move the *domains* folder to `/var/www/`
4. move the config/vhosts/somesite.tld.conf to `/etc/httpd/conf.d` or `/etc/apache2/sites-available` (depending on your flavour of linux)
5. in order to make the app work locally, you should map all domains found in `somesite.tld.conf` to `/etc/hosts`.

## Installation

1. install at /var/frameworks
2. sites go in /var/www/domains
3. be a sudoer
4. install PECL packages: mongodb, imagemagick, and tidy
5. php version must be > 5.1
6. mount your drive as -noatime,ACL,ext4
7. Have super fun-fun!!!


