<?php

namespace Dashifen\PrimaryCategory;

use Dashifen\Container\ContainerException;
use Dashifen\PrimaryCategory\Containers\Category;
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
		$this->addAction("admin_enqueue_scripts", "enqueueAssets");
		$this->addAction("save_post", "savePrimaryCategory");
		$this->addAction("delete_category", "deleteOrphanedCategories");
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
	 * enqueueAssets
	 *
	 * Adds our plugin's scripts and styles to the DOM.
	 *
	 * @return void
	 */
	protected function enqueueAssets(): void {

		// we only need to enqueue these assets when we're working with a
		// post type that is linked to the category taxonomy.  otherwise,
		// doing so is just a waste of time for the visitor.

		$screen = get_current_screen();
		$isCreate = $screen->action === "add" && $screen->base === "post";
		$isUpdate = ($_REQUEST["action"] ?? "") === "edit";
		if ($isCreate || $isUpdate) {
			$taxonomies = get_object_taxonomies(get_post_type());
			if (in_array("category", $taxonomies)) {
				$handle = $this->enqueue("assets/dash-10up-primary-category.js", ["jquery"]);

				// now, before we're done, we need to tell the JS objects
				// what categories are already chosen for this object so
				// the initial state of the primary category select element
				// can be set.  we get them with the method below, and then
				// add them to the JS scope.

				$jsObject = ["chosen" => $this->getPostCategories()];
				wp_localize_script($handle, "dashPrimaryCategories", $jsObject);
			}
		}
	}

	/**
	 * getPostCategories
	 *
	 * Returns an array of category terms that have been added to the
	 * current post.
	 *
	 * @return array
	 */
	protected function getPostCategories(): array {

		// returns the categories for the post we're currently editing.
		// the "fields" argument tells WP that we want these data as a
		// map of term IDs to names.  with that, and a quick check to
		// see if this post already has a primary category, we can build
		// the array that our JS uses to print the select element.

		$postId = get_the_ID();
		$wpCategories = wp_get_post_categories($postId, ["fields" => "id=>name"]);
		$primaryCategory = intval(get_post_meta($postId, self::PRIMARY_CATEGORY_META_KEY, true));

		foreach ($wpCategories as $termId => $name) {
			try {
				$categories[] = new Category($termId, $name, $primaryCategory === $termId);
			} catch (ContainerException $e) {

				// this shouldn't ever happen since we pass the Container
				// exactly what it needs to initialize it's ID and name.
				// but, in case it does, we don't want to halt the rest of
				// the page, so we're going to write this problem to the
				// log and fix it later.

				self::writeLog($e);
			}
		}

		return $categories ?? [];
	}

	protected function savePrimaryCategory($postId) {
		$primaryCategory = filter_input(INPUT_POST, "primary-category", FILTER_SANITIZE_NUMBER_INT);

		// if we could get our primary category value out of the data sent to
		// the server for this post, then we're going to update our post meta
		// so we can remember it.  otherwise, we delete that post meta so that
		// any prior record of a chosen primary category for this post is
		// removed.

		if (is_numeric($primaryCategory)) {
			update_post_meta($postId, self::PRIMARY_CATEGORY_META_KEY, $primaryCategory);
		} else {
			delete_post_meta($postId, self::PRIMARY_CATEGORY_META_KEY);
		}
	}

	/**
	 * deleteOrphanedCategories
	 *
	 * When a category is deleted, we end up here where we look for any
	 * posts that used it as their primary category and delete that
	 * information.
	 *
	 * @param int $termId
	 */
	protected function deleteOrphanedCategories(int $termId) {

		// TODO: enhancement - make this more efficient with a custom query

		$posts = get_posts([
			"fields"         => "ids",
			"posts_per_page" => -1,
			"meta_query"     => [
				[
					"key"   => self::PRIMARY_CATEGORY_META_KEY,
					"value" => $termId,
				],
			],
		]);

		foreach ($posts as $postId) {
			delete_post_meta($postId, self::PRIMARY_CATEGORY_META_KEY);
		}
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