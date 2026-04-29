<?php

// ============================================================
// Database — set via environment variables.
// For Apache add to your VirtualHost:
//   SetEnv DB_HOST localhost
//   SetEnv DB_USER bloguser
//   SetEnv DB_PASS secret
//   SetEnv DB_NAME blogdb
//
// For Nginx set in your PHP-FPM pool (/etc/php/x.x/fpm/pool.d/blog.conf):
//   env[DB_HOST] = localhost
//   env[DB_USER] = bloguser
//   env[DB_PASS] = secret
//   env[DB_NAME] = blogdb
// ============================================================
$db_host = getenv('DB_HOST');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');
$db_name = getenv('DB_NAME');

$db = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($db->connect_error) die("Database connection failed: " . $db->connect_error);
$db->set_charset('utf8mb4');


// ============================================================
// Site settings — edit these to customise your blog.
// There is no UI for these; just change the values here.
// ============================================================

// Blog identity
define('SITE_TITLE',    'My Blog');
define('SITE_SUBTITLE', 'Thoughts, notes and ideas.');
define('SITE_FOOTER',   'My Blog &copy; ' . date('Y'));

// Logo: path relative to the site root, or empty string to show SITE_TITLE as text.
// Example: 'uploads/logo.png'
define('SITE_LOGO', '');

// Favicon: path relative to the site root, or empty string for none.
// Example: 'uploads/favicon.ico'
define('SITE_FAVICON', '');


// ============================================================
// Homepage category chips — set to false to hide the
// "Browse by category" section on the homepage.
// ============================================================
define('SHOW_CATEGORY_CHIPS', true);


// ============================================================
// TinyMCE — set via environment variable TINYMCE_API_KEY.
// Nginx PHP-FPM pool:  env[TINYMCE_API_KEY] = your-key-here
// Apache VirtualHost:  SetEnv TINYMCE_API_KEY your-key-here
// Falls back to 'no-api-key' if the variable is not set.
// ============================================================
define('TINYMCE_API_KEY', getenv('TINYMCE_API_KEY') ?: 'no-api-key');
