iCoworker
=========
Project initial wiki page http://trac.htk.tlu.ee/icoworker

Requirements
============

1. Apache with mod_php
2. PHP version 5.4 or above (due to Facebook SDK 4)
 * Make sure your version has **mysql extension** (is deprecated in newer versions)
 * Extensions: gettext, json (possibly others that are part of the default installaton package)

Installation
============
1. Move htaccess_example to .htaccess
2. Create folders (OPTIONAL):
 * Datastore - data like images and other files for config::DATA_STORE
 * Session - session data for config::SESSION_SAVE_PATH
 * Datastore can contain session folder
 * Edit config.php to point them out. Makse sure that apache can write into them. 
3. If you chose not to create datafolders some constants should be disabled in config.php
 * DATA_STORE
 * PHPTAL_TMP
 * Optionally you could also disable SESSION_SAVE_PATH
4. Please make sure that FACEBOOK is enabled and APP_ID along with APP_SECRET are provided.
5. Disable DEV_MODE in production environment
6. Use composer to install additional packages (install globally or download composer.phar and run **php composer install**; this will create the vendor directory)

Please note that database and tables will be created automatically if one does not exists.
You can always use the file to create tables manually.
File is located in engine/lib/database/teke.sql.
Please replace prefix_ with something you chose in that case.

Please note that initial admin role has to be granted to a user with direct query.
Set role to 9
Later on that administrator may change roles for others using the GUI
