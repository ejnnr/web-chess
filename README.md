web-chess
================
Develop:
[![Build Status](https://travis-ci.org/jupiter24/web-chess.svg?branch=develop)](https://travis-ci.org/jupiter24/web-chess)
Master:
[![Build Status](https://travis-ci.org/jupiter24/web-chess.svg?branch=master)](https://travis-ci.org/jupiter24/web-chess)

A web implementation of a chess database program like SCID.

Setting it up on your local machine
==========================================

You should have Apache, PHP and MySQL installed and running.

0.  Setup Apache, PHP and MySQL if you haven't already
1.  Clone the repository
2.  Create a MySQL database for web-chess
3.  (optional) Create a user called webchess and give it access to the database you just created (you can also use a pre-existing user if you want)
4.  Install composer (http://getcomposer.org)
5.  run `composer install` in your projects root directory
6.  copy .env.example to .env and enter your MySQL credentials there
7.  run `php artisan key:generate` in your project root
8.  run `php artisan migrate` in your project root
9.  install nodejs (https://nodejs.org/)
10. run `npm install --global bower` (you might need sudo)
11. run `npm install --global gulp`
12. run `bower install` in your project root


WARNING: Since this projects is in an extremely early stage of development, there will often be changes which require you to set up additional database tables or change anything else.
If you commit such a change, please also leave a notice here where you explain, what other people have to do to perform the same changes on their machines.
You should also put some kind of note into your commit message.


The test directory
========================

The .gitignore file tells git to ignore everything inside test/ .
So if you want to test some of the PHP functions in include/, but the frontend to do so isn't implemented yet, please use this directory (of course only if your test file is something temporary, which you are going to delete after performing the test).

UI Concepts
==============
Modes: A mode is a set of options which determine e.g. which windows are shown, whether the next move and other information is hidden (useful for tactics/Guess the Move) and even which database is opened.
This can be very useful if used effectively. For example you can create a mode for opening preparation, where the tree view is shown and your preferred database for this task is opened. Or you can create one where engine, annotation window and the database with your own games are opened for going over the games you played recently.
There will be some default modes using the example databases for the different situations but of course you can delete/modify them and make your one.
You can switch between different modes very swiftly using the mode navigation bar on the right side.

Databases: You probably know a database as a collection of chess games. But in WebChess that's only one of several possible types of databases. A database can also be a tactics database, containing tactic exercises (basically they're also just PGN files with an custom FEN as starting position, but a tactics database can contain additional attributes such as difficulty or whether or not you have solved an exercise correctly already).
Another possibility is a repertoire database, which takes care of transpositions automatically.
Another advantage of these different types of databases is, that WebChess will automatically open specific windows, depending on which type of database you just opened (of course this behaviour can be modified or turned off entirely). Let's say for example you just opened a tactics database. WebChess will automatically start the tactics trainer with that particular database.
