<?php

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'app');
define('DB_USER', getenv('DB_USER') ?: 'appuser');
define('DB_PASS', getenv('DB_PASS') ?: 'apppass');

define('SITE_URL', getenv('SITE_URL') ?: 'http://localhost:8080');
define('MAIL_FROM', getenv('MAIL_FROM') ?: 'noreply@your-domain.de');
define('WEBSITE_NAME', getenv('WEBSITE_NAME') ?: 'Wuffi Let\'s Go!');