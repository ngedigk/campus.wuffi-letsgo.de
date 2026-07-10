<?php

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'app');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

define('SITE_URL', getenv('SITE_URL') ?: 'http://localhost:8080');
define('MAIL_FROM', 'noreply@your-domain.de');