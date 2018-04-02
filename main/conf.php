<?php
/**
 * =========================== Every Configuration Files Start Here ===========================
 * Notice: Comment out all the
 */

// Define the Constance DNS
define("DNS", "//".Route::getDomainName());


// Define All of the Global Variables
Config::set("SiteName", "LeafDev Digital Solution");
Config::set("CDN", DNS);
Config::set("DNS", DNS);

// Define Directories
Config::set("VENDOR_DIR", DNS. "/vendor");
Config::set("CSS_DIR", DNS. "/main/res/css");
Config::set("JS_DIR", DNS. "/main/res/js");
Config::set("IMG_DIR", DNS. "/main/res/img");
Config::set("JSON_DIR", DNS. "/main/res/json");

// MySQL Database Credentials Configuration
define("MySQL_host", "127.0.0.1");
define("MySQL_user", "root");
define("MySQL_pass", "");
define("MySQL_dbn", "purplebee");

// MongoDB Database Credentials Configuration
define("mongoHost", "127.0.0.1");
define("mongoPort", "27017");
define('mongoDBName', "leafTabulation");

// Admin Authentication
define('mongoUser', "leaf");
define('mongoPass', "mldev126");

// Client Authentication
define('mongoUserClient', "client");
define('mongoPassClient', "123456");
