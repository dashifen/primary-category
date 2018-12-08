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

	// this plugin is encapsulated within the PrimaryCategory object.
	// it's initialize method adds hook handlers to the WP actions
	// necessary for its operation.

	$primaryCategory = new PrimaryCategory();
	$primaryCategory->initialize();
} catch (Exception $e) {

	// any exceptions that make there way here, we'll catch with a
	// default behavior.  that behavior is to write the data about the
	// exception to the WP log unless WP_DEBUG is set, in which case
	// its data is dumped on screen.

	PrimaryCategory::catcher($e);
}