<?php

define('DB_HOST', $_ENV['DB_HOST'] ?: 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?: 'app');
define('DB_USER', $_ENV['DB_USER'] ?: 'appuser');
define('DB_PASS', $_ENV['DB_PASS'] ?: 'apppass');

define('SITE_URL', $_ENV['SITE_URL'] ?: 'http://localhost:8080');
define('MAIL_FROM', $_ENV['MAIL_FROM'] ?: 'noreply@your-domain.de');
define('WEBSITE_NAME', $_ENV['WEBSITE_NAME'] ?: 'Wuffi Let\'s Go!');