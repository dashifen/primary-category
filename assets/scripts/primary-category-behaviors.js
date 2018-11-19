const arrayIncludes = require("array-includes");

export default {

	// $ === jQuery

	initialize($) {
		const checkboxes = $("#categorychecklist");
		const categoryAddButton = $("#category-add-submit");

		// if someone loads the screen, marks boxes, and then soft-refreshes
		// it, browsers usually remember the marks they added.  but, since
		// those marks never made it to the server, we won't have been
		// delivered information about them.  therefore, we'll call the
		// updateOptions() method to make sure we're good to go before we
		// do anything else.

		this.updateOptions(checkboxes);

		checkboxes.click((event) => {

			// since categories might get added while this screen is loaded,
			// we watch the div containing the checkboxes for clicks and not
			// the inputs themselves.  so, before we do anything, we need to
			// be sure this is an event we care about.

			if (event.target.tagName === "INPUT" && event.target.type === "checkbox") {

				// looks like we care about it.  what we do is based on the
				// checked state of the box.  if it's not checked, we remove
				// this category from our state.  otherwise, we add it.

				!event.target.checked
					? this.removeCategory(event.target)
					: this.addCategory(event.target);
			}
		});

		categoryAddButton.click(() => {
			const newCategory = $("#newcategory");
			const newCategoryName = newCategory.val();

			// this one is weird because WordPress uses ajax to send the
			// new category we're adding to the sever.  the returned response
			// to that call is HTML, so we can't easily use it for our
			// purposes here.  instead, we capture the name of our new
			// category (above).  then, we set a 100ms interval to watch
			// for when WordPress empties the name field when it's done
			// with it's ajax-ing.

			const intervalId = window.setInterval(() => {
				if (newCategory.val() !== newCategoryName) {

					// once we find our way in here, we want to clear our
					// interval so it stops looping and we don't kill the
					// client's machine.  then, we update our options using
					// the method below.

					window.clearTimeout(intervalId);
					this.updateOptions(checkboxes);
				}
			}, 100)
		})
	},

	updateOptions(checkboxes) {

		// this method is called anytime that the state of our on-screen
		// checkboxes might be different from what's in state object for
		// our component.  in order to be sure that we correctly identify
		// and skip the options that are already in that state, we'll get
		// only the IDs out of it using Array.map() as follows.

		const chosenIds = window.dashPrimaryCategories.chosen.map((choice) => {
			return choice.id;
		});

		checkboxes.find(":input").each((i, checkbox) => {
			if ($(checkbox).prop("checked")) {

				// IE never got the Array.includes method.  if some poor soul
				// is still using it, we've included the arrayIncludes()
				// polyfill just for them.  if the chosenIds array we created
				// above does not include the checkbox that we're looking at
				// in this iteration, then we add it to our options with the
				// method below.

				if (!arrayIncludes(chosenIds, Number(checkbox.value))) {
					this.addCategory(checkbox);
				}

			}
		});
	},

	removeCategory(checkbox) {

		// our Vue component is watching the window.dashChosenCategories
		// object to know about the options that it should load into its.
		// select element.  so, when a category checkbox is unmarked, we
		// need to remove it from that object, which will in turn update
		// our component.  since it's an array, we can use filter to find
		// and remove checkbox from the list.

		window.dashPrimaryCategories.chosen = window.dashPrimaryCategories.chosen.filter((category) => {

			// filter keeps anything that returns true.  we want to keep the
			// ones that aren't checkbox, so we want to compare category
			// to checkbox and when they don't match, we return true as
			// follows.

			return category.id !== Number(checkbox.value);
		});
	},

	addCategory(checkbox) {

		// adding to our state is more straightforward that removing since
		// we don't need any filters.  we just push our new item onto the
		// array and sort so that we keep things in alphabetical order.

		window.dashPrimaryCategories.chosen.push({
			"id"       : Number(checkbox.value),
			"name"     : checkbox.nextSibling.data.trim(),
			"isPrimary": false,
		});

		window.dashPrimaryCategories.chosen.sort(function(a, b) {
			return a.name < b.name ? -1 : 1;
		});
	}
};