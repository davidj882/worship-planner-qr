# Requirements #

Worship Planner QR requires a web server running MySQL and PHP 4.3 or newer. It has been tested most extensively with the Apache web server, but also used successfully on some Windows servers.

Version 0.9 was updated to resolve some problems associated with changes in the default settings in PHP 5. Testing on my systems indicates that all features are working properly, but please let me know if you experience problems. Version 0.95 has some improved security, and a better email feature.


# Downloading #

The current version can be obtained from this site.  If, for some reason you want to obtain an old version you can go to the old worship planner site: http://www.worshipplanner.net.


# Installation #

## First-Time Installation ##

Installation is fairly straightforward if you know how to create a database on your web host. Here are the required steps:

  1. Create a MySQL database to hold the information for Worship Planner QR. Call the database whatever you like, as long as you can remember the name. Next, create a user and password, granting access to the database you created.
  1. Download the source files, and unzip them to any folder you like on your web server (we will assume the folder is called `<your web page url>/WorshipPlanner`. Also create a directory to which sheet music can be uploaded. You will have to give write permissions on this directory by using the user interface provided by your hosting company, or using `chmod 777 <directory name>`
  1. Modify the file `var_config.php` in a text editor, filling in the appropriate database name, user name, password, etc. You must also specify the date on which the script will begin creating records. This must be a Sunday, or Saturday, or whichever day you hold your worship gatherings! Most likely this will be next Saturday or Sunday's date. You should specify an end date that is a few years in the future (though it is possible to add more dates at a later time by repeating some of these steps)
  1. Once you've done this, and the files are on your web server, open the document called `createtables.php`. Assuming you used the directory name given above, you would type in your web browser location bar: `http://<your web page url>/WorshipPlanner/createtables.php`
  1. Next open the file `createdates.php` in the same manner.
  1. Finally go to `<your web page url>/WorshipPlanner`, or the directory where you installed Worship Planner QR. Log in with the default user name Admin and password admin. For security you should change the password. You could also change the permissions on the files `createtables.php` and `createdates.php` using `chmod 400` so that no one can open these files at a later time.

You're ready to start using the program! If you encounter errors, please contact me so I can make Worship Planner QR better for everyone!

## Upgrading from a Previous Version ##

  1. Make sure you record your settings from var\_config.php (In a future version hopefully I will have the capability to automatically transfer your settings, but for now you'll have to re-enter them.  Sorry!)
  1. Download and unzip the new version, update your settings in var\_config.php, and upload to your server.
  1. Run the script upgrade.php, which will add some database tables.  You should be ready to go!