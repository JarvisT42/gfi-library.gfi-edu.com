<?php
// Explicit logging setup
ini_set("log_errors", 1);
ini_set("error_log", "./error_log.log");
error_reporting(E_ALL);

// Deliberate error for testing
trigger_error("This is a test error for logging!", E_USER_WARNING);
