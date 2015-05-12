# Version 1.17 Beta #
  * Added support for SSL when sending mail via SMTP (required by some hosts such as Gmail)
  * Added the ability to specify a specific port (other than port 25) when sending mail via SMTP (again, Gmail and others do not use port 25).
  * Fixed problem with autoincrementing fields.  The code did not work properly with newer versions of PHP/MySQL, when trying to add users, songs, etc, to the database.  This also created a problem with the initial setup script on some platforms.
  * **Note**: I marked this as beta because I haven't had the chance to test the changes extensively.  However, I don't anticipate any problems, and this version should fix some issues that some users were experiencing.

# Version 1.16 #
  * IMPORTANT: underscores are no longer replaced by spaces in passwords and other information submitted to the database.  If you upgrade to this version, you may need to change passwords that contain underscores, or use a space instead of an underscore when entering the password.
  * Fixed a bug where mysql\_real\_escape\_string was not properly called on information submitted to the database
  * Fixed a bug from version 1.15 where some custom fields (used for development) were not removed properly

# Version 1.15 #

  * Added simple form to change song order on planning/programming page
  * Fixed songlist to include songs that begin with numbers
  * Ability to add custom fields to the Dates table (undocumented at this time). Hint: add the fields you want to customFields.php, then run the script addCustomFields.php.  Now the custom fields should show up when you click "Edit Date Info" on the programming page.