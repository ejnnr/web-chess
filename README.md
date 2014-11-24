web-chess
================

A web implementation of a chess database program like Scid.

Setting it up on your local machine
==========================================

You should have Apache, PHP and MySQL installed and running.

1. Clone the repository
2. Create a MySQL database called webchess (notice: You can change all of these names, but you also have to change them in include/config.php accordingly if you do so)
3. Create a user called webchess and give it acces to the database you just created (this is only for security reasons, you can also use root)
4. Enter the users password into include/config.php or take the example password from there for your user (NOT RECOMMENDED!!!)
5. If you are using phpMyAdmin, you can simply go into the newly created database, select "Import" and select webchess.sql from the reporitory to set up the tables in the database. If you use another interface, just look into the webchess.sql file to see which tables you have to create.

WARNING: Since this projects is in an extremely early stage of development, there will often be changes which require you to set up additional tables or change anything else.
If you commit such a change, please also leave a notice here where you explain, what other people have to do to perform the same changes on their machines.
You should also put some kind of note into your commit message.


The test directory
========================

The .gitignore file tells git to ignore everything inside test/ .
So if you want to test some of the PHP functions in include/, but the frontend to do so isn't implemented yet, please use this directory (of course only if your test file is something temporary, which you are going to delete after performing the test).

Changes after commit https://github.com/jupiter24/web-chess/commit/f3b6f7e8e3a69057af2abad2e80e165d5ced0f90
===================================================================================================================

In the commit just mentioned, the column 'salt' in the table 'users' isn't used anymore.
You have to delete this column in order for your web-chess instance to work properly if you are using the latest code.
The SQl code do do so should look like this:  
ALTER TABLE `users` DROP `salt`;
