<?php
# Global App Constants
define("APP_URL", "localhost/payment-logger");
define("APP_LOGO_URL", "assets/images/logo.png");
define("APP_VERSION", "1.0.0");

# Page Settings
define("INDEX_PAGE", "index.php");
define("DASHBOARD_PAGE", "dashboard.php");

# MailGun API Credentials
define("API_KEY", "");
define("APP_DOMAIN", "");
define("API_HOSTNAME", "");

# Email Setting
define("FROM", "");

# Giant SMS Setting
define("SMS_API_USERNAME", "");
define("SMS_API_SECRET", "");
define("SMS_SENDER_ID", "");
define("SMS_TOKEN", "");

# MySql Resource Database Credentials
define("DB_HOST", 'localhost');
define("IDENTITY_DATABASE", ''); // For Identity Database
define("RESOURCE_DATABASE", 'payment_logger'); // Change this to your database name
define("DB_USERNAME", 'root');
define("DB_PASSWORD", '');

# PostgreSql Resource Database Credentials
define("PG_DB_HOST", '127.0.0.1');
define("PG_DB_PORT", '5432');
define("PG_DB_NAME", '');
define("PG_DB_USERNAME", 'postgres');
define("PG_DB_PASSWORD", '');



define("MAX_LOGIN_ATTEMPTS_PER_HOUR", 5);
define("MAX_EMAIL_VERIFICATION_REQUESTS_PER_DAY", 3);
define("MAX_PASSWORD_RESET_REQUESTS_PER_DAY", 3);
define("PASSWORD_RESET_REQUEST_EXPIRY_TIME", 60 * 60);
define("CSRF_TOKEN_SECRET", '<change me to something random>');

# Code we want to run on every page/script
date_default_timezone_set("UTC");
