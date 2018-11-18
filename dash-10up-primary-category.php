<?php
/*!
Plugin Name: Dashifen's Primary Category
Description: A plugin that allows a post author to choose a primary category.
Author URI: https://dashifen.com
Author: David Dashifen Kees
Version: 1.0
*/

require "vendor/autoload.php";

use Dashifen\PrimaryCategory\PrimaryCategory;

try {
	$primaryCategory = new PrimaryCategory();
	$primaryCategory->initialize();
} catch (Exception $e) {
	PrimaryCategory::catcher($e);
}