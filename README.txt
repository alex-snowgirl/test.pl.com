Test WEB & API project for poland Hosting Company
A lot of TODOs present, due to other interviews.

FULLY(!) written by me (except jquery.js, fonts, IMGs).

This project follows MVP concept,
that means - MINIMUM VIABLE PRODUCT,
that means - if you find something not fully developed or implemented, - probably I know about this,
and could easily fix, improve, add or build that (those) things,


Please take into account - this is a TEST(!) project

-Simple Config File-
Location - config.ini

-PHP-
@todo PHP7.1
@TODO no any security layer!!!
1) save internal state - client id
2) compare request id with stored one
3) check request quantity
4) validate request params (user ids, rating marks, balance updates, etc...)
@todo tests
@todo async requests
@todo transactions
@todo cache
@todo docker file

Main Classes:
1) CORE\App - Simple Universal Application class
2) CORE\Web\Request - Simple Universal Web Request manager
3) CORE\Web\Response - Simple Universal Web Response manager
4) CORE\Entity - Simple Entity (ORM) and Entity Manager @todo split...
5) APP\Web\HandlerContainer - Simple WEB requests handler (implemented instead of well-known Controller-Action pattern)
6) APP\Api\HandlerContainer - Simple API requests handler (implemented instead of well-known Controller-Action pattern)
7) APP\Entity\User - Simple User Entity & Manager ("user" table management)
8) APP\Entity\Product - Simple Product Entity & Manager ("product" table management)
9) APP\Entity\Order - Simple Order Entity & Manager ("order" table management)
10) APP\Entity\Delivery - Simple Delivery Entity & Manager ("delivery" table management)
11) APP\Entity\Product\Rating - Simple Product-Rating Entity & Manager ("product_rating" table management)
12) APP\Entity\Order\Product - Simple Order-Product Entity & Manager ("order_product" table management)
13) CORE\RDBMS - Simple Core Relation DB Manager
14) CORE\RDBMS\MySQL - Simple MySQL realization of CORE\RDBMS (in use)
15) Application was made following OOP(!) and SOLID(!) principles
15) And more-more other things...

-DB-
1) INNODB table type (for transaction support)
2) Table columns types and sizes was designed for test purposes
3) Indexes and Foreign Keys present
4) For more details - please use mysql client and observer or use something like HeidiSQL or others...

-JavaScript-
Main File:
1) pub/app.js - fully responsible for the Client application
2) App uses Browser local and session DBs
3) App uses API APP of Backend Application (api.php)
4) And more-more other things...
@todo pre-processors, minify
@todo split into separate classes (entity, storage, Product, User, Rating, view etc.)
@todo add error handlers
@todo implement promises
@todo improve cache

-CSS-
1) pub/app.css - fully responsible for the Client look and feel
@todo pre-processors, minify
@todo split into separate files page and app styles

-Shop-
1) This is Shop simulator
2) Enter your name and balance (lets say - registration)
3) Add products to your cart
4) Increase/Decrease your product cart quantities
5) Make order
6) Choose delivery type
7) Pay (lets say - payment)

8) You could refresh page on any step(!)
9) You could use Back button - to get on previous state
10) You could use Clear button - to clear all Browser data and begin your awesome trip from zero

Dependencies (Tested on):
1) SL: PHP 5.6
2) PHP ext: json, mysqli
3) PHP composer
4) MYSQL: 10.0.30-MariaDB-0+deb8u1
5) Server: Apache 2.4
6) Apache: php mod
7) OS: Debian

Usage:
1) cd /path/to/project
2) composer install

#if you aren't using dedicated web-server - you could use PHP's one
#cd pub & php -S 0.0.0.0:8080 & http://0.0.0.0:8080/web.php

3) setup PHP + dependencies
4) setup Apache virtual host + dependencies
5) setup MYSQL database & do migrations
6) enjoy

Total time spent in general: 8 hours.

deployed to - snowgirl.cba.pl
Questions - alex.snowgirl@gmail.com


