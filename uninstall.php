<?php

require_once "vendor/autoload.php";

use Dashifen\PrimaryCategory\PrimaryCategory;

if (!defined("WP_UNINSTALL_PLUGIN")) {

	// prevents direct access to this file; the constant is defined by
	// WP core when uninstalling plugins.

	die;
}

PrimaryCategory::uninstall();