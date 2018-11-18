<?php

namespace Dashifen\PrimaryCategory;

use Dashifen\WPHandler\Handlers\AbstractPluginHandler;
use Dashifen\WPHandler\Hooks\HookException;

class PrimaryCategory extends AbstractPluginHandler {
	public const PRIMARY_CATEGORY_META_KEY = "_dash_10up_primary_category";

	/**
	 * initialize
	 *
	 * Uses addAction() and addFilter() to connect WordPress to the methods
	 * of this object's child which are intended to be protected.
	 *
	 * @return void
	 * @throws HookException
	 */
	public function initialize(): void {
		$this->addAction("admin_enqueue_scripts", "enqueueScripts");
		$this->addAction("admin_enqueue_scripts", "enqueueStyles");
	}

	/**
	 * getPluginDirectory
	 *
	 * Returns the name of the directory for this plugin so that our parent
	 * object knows where we live.  This avoids the need for a ReflectionClass
	 * in that parent.
	 *
	 * @return string
	 */
	protected function getPluginDirectory(): string {
		return "dash-10up-primary-category";
	}


	/**
	 * enqueueScripts
	 *
	 * Adds our plugin's scripts to the DOM.
	 *
	 * @return void
	 */
	protected function enqueueScripts(): void {
		$this->enqueue("assets/dash-10up-primary-category.js", ["jquery"]);
	}

	/**
	 * enqueueStyles
	 *
	 * Adds our plugin's styles to the DOM.
	 *
	 * @return void
	 */
	protected function enqueueStyles(): void {
		$this->enqueue("assets/dash-10up-primary-category.css");
	}

	/**
	 * uninstall
	 *
	 * If this plugin is uninstalled, we want to remove the information about
	 * primary categories from the postmeta database table.
	 *
	 * @return void
	 */
	public static function uninstall(): void {

		// TODO: enhancement - make this more efficient with a custom query

		$posts = get_posts([
			"fields"         => "ids",
			"posts_per_page" => -1,
			"meta_query"     => [
				[
					"key"     => self::PRIMARY_CATEGORY_META_KEY,
					"compare" => "EXISTS",
				],
			],
		]);

		foreach ($posts as $postId) {
			delete_post_meta($postId, self::PRIMARY_CATEGORY_META_KEY);
		}
	}
}