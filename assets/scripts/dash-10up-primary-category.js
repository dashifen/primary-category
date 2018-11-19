import Vue from "vue";
import PrimaryCategory from "./primary-category.vue";
import PrimaryCategoryBehaviors from "./primary-category-behaviors.js";

Vue.config.productionTip = false;

// this is the core JS file for the plugin.  it identifies whether or not
// the other JS capabilities and behaviors have to be initialized and then,
// if they do, does so.

(function($) {
	$(document).ready(function () {
		const categories = $("#taxonomy-category #category-all");
		if (categories) {

			// now that we've confirmed that the category taxonomy is listed
			// on this screen, we want to add our primary category component
			// after it.  then, we can instantiate a Vue object that will
			// handle the rest of our needs with respect to that component.
			// notice that we put our component inside a <div> that we use
			// as our Vue root.  this is to avoid nixing any of the other
			// event observations on other parts of the WP interface.

			const primaryCategory = $(
				'<div id="primary-category-vue-root"><primary-category></div>'
			);

			categories.after(primaryCategory);

			new Vue({
				components: { PrimaryCategory },
				el: "#primary-category-vue-root"
			});

			// now that our component has been created, we need to add some
			// more behaviors that exist outside of it.  these are mostly
			// event observations on the default DOM structures of WP that
			// we need in order to operate our component effectively.

			PrimaryCategoryBehaviors.initialize($);
		}
	});
})(jQuery);

