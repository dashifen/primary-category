import Vue from "vue";
import PrimaryCategory from "./primary-category.vue";
import PrimaryCategoryBehaviors from "./primary-category-behaviors.js";

Vue.config.productionTip = false;

(function($) {
	$(document).ready(function () {
		const categories = $("#taxonomy-category #category-all");
		if (categories) {

			// now that we've confirmed that the category taxonomy is listed
			// on this screen, we want to add our primary category component
			// after it.  then, we can instantiate a Vue object that will
			// handle the rest of our needs with respect to that component.

			const primaryCategory = $('<div id="primary-category-vue-root"><primary-category></div>');
			categories.after(primaryCategory);

			new Vue({
				components: { PrimaryCategory },
				el: "#primary-category-vue-root"
			});

			// now that our component has been created, we need to add some
			// more behaviors that exist outside that component.  these are
			// hooks on the default DOM structures of WP that we need in
			// order to operate our component effectively.

			PrimaryCategoryBehaviors.initialize($);
		}
	});
})(jQuery);

