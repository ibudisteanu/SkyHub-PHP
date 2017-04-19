(C) CopyRight 2016 - 2017 SC BIT TECHNOLOGIES RO SRL-D

------------------------------------------------------------------------------------
---------------------------------INSTALLATION---------------------------------------
------------------------------------------------------------------------------------

Requirements

1. XAMPP (Windows) or LAMP (Unix). You need to install Apache and PHP. It is recommended to have XAMPP (Windows) or LAMP for Unix because it comes with a control panel
2. Mongo Server to host your own Mongo database server and to work corrupt the online database

3. Mongo Client Driver for php.
        The driver can be found here: https://pecl.php.net/package/mongo Windows

        Windows version:
            a. Download the .dll from PECL website
            b. Move it to php/extensions folder
            c. Edit php.ini and add "extension=mongo.dll"

        Unix version:
            a. Download the source code
            b. Compile it
            c. Move to the compiled driver to php/extensions folder
            d. Edit php.ini and add "extension=mongo.dll"

            Obs: Not sure about b,c,d for Unix because I have failed to do it

Installation Tutorial:

1. To install the social network, you just need to download the entire repository and move it in the www (htdocs) folder of the Apache

2. Just start the website, because it can start without users and content
2. Download the Mongo Database and install in your LOCAL mongo database server.

Obs. Debian : you need json_library for PHP to be installed in PHP

----------------------------------------------------------------------------------
------------------------------------MongoDB---------------------------------------
----------------------------------------------------------------------------------

How to Open MONGODB

cd G:\Program Files\MongoDB\Server\3.4\bin\
mongod.exe --dbpath D:\data\dbSkyHub\

Pwd: hbdsahj23123cc

mongo ds011725.mlab.com:11725/skyhub_db -u MONGO_CLIENT -p MONGO_PASSWORD

//*****************************************************BAT TO OPEN MONGODB*********************************************\\

start /b "C:\Program Files\MongoDB\Server\3.2\bin\" "C:\Program Files\MongoDB\Server\3.2\bin\mongod.exe" --dbpath "D:\data\dbSkyHub"
pause

//**********************************************IMPORT / DOWNLOAD ONLINE DATA******************************************\\
//LOAD from online to local db

Instructions: You need to download the data step 1. and then to install the data in the database step 2.

    //backup localhost
    1. mongodump -h ds011725.mlab.com:11725 -d skyhub_db -u MONGO_CLIENT   -p MONGO_PASSWORD -o  E:\data\
                                                                                                E:\data
    //download from the mongo server
    mongorestore -h localhost -d HubDB_backup D:\DB\Backup\skyhub_db\
    2. mongorestore -h localhost -d HubDB E:\data\skyhub_db\

//DONT USE IT
//*****************************************EXPORT / UPLOAD LOCAL DATA TO THE INTERNET**********************************\\
//install localdb to online

    //backup localhost
    1. mongodump -h localhost -d HubDB -o E:\data\upload\HubDB\
    //upload on the mongo server
    2. mongorestore -h ds011725.mlab.com:11725 -d skyhub_db -u MONGO_CLIENT  -p MONGO_PASSWORD E:\data\upload\HubDB\


//*******************************************************COMPOSER ****************************************************\\

1. Composer software Installation
    1. Install Composer if you don't have it,
    2. In case it shows a missing DLL search it and replace it in the PHP

2. Composer Usage
    http://stackoverflow.com/questions/29441950/how-to-use-composer-on-windows
    1. cd C:\xampp\htdocs\mywebsite
    2. composer install
-----------------------------------------------------------------




//************************BOOTSTRAP XXS and XXS-TN******************************
BOOTSTRAP XXS AND XXS-TN https://github.com/auipga/bootstrap-xxs


//*******************************************************CACHE DRIVER****************************************************\\

    The Cache driver will not work if the "application\Cache" folder and the .htaccess file don't exist!!

    Memcached:
        Additionally, you can also install for Windows the Memcached
            https://commaster.net/content/installing-memcached-windows





###################
What is CodeIgniter
###################

CodeIgniter is an Application Development Framework - a toolkit - for people
who build web sites using PHP. Its goal is to enable you to develop projects
much faster than you could if you were writing code from scratch, by providing
a rich set of libraries for commonly needed tasks, as well as a simple
interface and logical structure to access these libraries. CodeIgniter lets
you creatively focus on your project by minimizing the amount of code needed
for a given task.

*******************
Release Information
*******************

This repo contains in-development code for future releases. To download the
latest stable release please visit the `CodeIgniter Downloads
<https://codeigniter.com/download>`_ page.

**************************
Changelog and New Features
**************************

You can find a list of all changes for each release in the `user
guide change log <https://github.com/bcit-ci/CodeIgniter/blob/develop/user_guide_src/source/changelog.rst>`_.

*******************
Server Requirements
*******************

PHP version 5.4 or newer is recommended.

It should work on 5.2.4 as well, but we strongly advise you NOT to run
such old versions of PHP, because of potential security and performance
issues, as well as missing features.

************
Installation
************

Please see the `installation section <https://codeigniter.com/user_guide/installation/index.html>`_
of the CodeIgniter User Guide.

*******
License
*******

Please see the `license
agreement <https://github.com/bcit-ci/CodeIgniter/blob/develop/user_guide_src/source/license.rst>`_.

*********
Resources
*********

-  `User Guide <https://codeigniter.com/docs>`_
-  `Language File Translations <https://github.com/bcit-ci/codeigniter3-translations>`_
-  `Community Forums <http://forum.codeigniter.com/>`_
-  `Community Wiki <https://github.com/bcit-ci/CodeIgniter/wiki>`_
-  `Community IRC <https://webchat.freenode.net/?channels=%23codeigniter>`_

Report security issues to our `Security Panel <mailto:security@codeigniter.com>`_
or via our `page on HackerOne <https://hackerone.com/codeigniter>`_, thank you.


