<?php

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type: text/html; charset=utf-8');
header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');


define("DBHOST", "localhost");
define("DBUSER", "root");
define("DBPASSWORD", "");
define("DBNAME", "dury");

define("DEFAULT_LANGUAGE_ID", 1);
define("DEFAULT_LANGUAGE_NAME", "Danish");
?>