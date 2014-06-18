<?php

# CONSTANTS

define('ROOT_URI', 'http://localhost/public_html/SOGI/');

define('ROOT_PATH', dirname(__FILE__) . '/');
define('CONT_PATH', ROOT_PATH . 'content/');
define('INCL_PATH', ROOT_PATH . 'include/');
define('SESS_PATH', ROOT_PATH . 'session/');

# GLOBALS

/**
 * Array of filenames that cannot be uploaded
 */
global $FILENAME_BAN;
$FILENAME_BAN = array('.', '..', 'CONFIG', 'CONSOLE', '.htaccess', 'php.ini');

# REQUIRES

require_once(INCL_PATH . 'functions.lib.php');
require_once(INCL_PATH . 'session.class.php');

?>