const arrayIncludes = require("array-includes");

export default {

	// $ === jQuery

	initialize($) {
		const checkboxes = $("#categorychecklist");
		const categoryAddButton = $("#category-add-submit");

		// if someone loads the screen, marks boxes, and then soft-refreshes
		// the screen, browsers usually remember the marks they added.  but,
		// since those marks never made it to the server, we won't have been
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

		categoryAddButton.click((event) => {
			const newCategory = $("#newcategory");
			const newCategoryName = newCategory.val();

			// this one is weird because WordPress uses ajax to send the
			// new category we're adding to the sever.  the returned response
			// to that call is HTML, so we can't easily use it for our
			// purposes here.  instead, we capture the name of our new
			// category (above).  then, we set a 100ms interval to watch
			// for when WordPress empties the name field when it's done
			// with it's ajax-ing.

			let intervalId;
			intervalId = window.setInterval(() => {
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
		// our component.

		const chosenIds = window.dashPrimaryCategories.chosen.map((choice) => {
			return choice.id;
		});

		checkboxes.find(":input").each((i, checkbox) => {
			if ($(checkbox).prop("checked")) {

				// IE never got the Array.includes method.  if some poor soul
				// is still using it, we've included the arrayIncludes()
				// polyfill just for them.

				if (!arrayIncludes(chosenIds, Number(checkbox.value))) {
					this.addCategory(checkbox);
				}

			}
		});
	},

	removeCategory(checkbox) {

		// our Vue component is watching the window.dashChosenCategories
		// object to know about the options that it should load into it.
		// so, when a category checkbox is unmarked, we need to remove it
		// from that object, too.  since it's an array, we can use filter
		// to find and remove it.  we want to remove the one that matches
		// our checkbox's value, so when filtering, we return true as
		// long as the IDs don't match.  thus, everything _except_ the
		// matching one is kept.

		window.dashPrimaryCategories.chosen = window.dashPrimaryCategories.chosen.filter((category) => {
			return category.id !== Number(checkbox.value);
		});

		window.dashPrimaryCategories.chosen.sort(this.optionSorter);
	},

	optionSorter(a, b) {
		return a.name < b.name ? -1 : 1;
	},

	addCategory(checkbox) {
		window.dashPrimaryCategories.chosen.push({
			"id"       : Number(checkbox.value),
			"name"     : checkbox.nextSibling.data.trim(),
			"isPrimary": false,
		});

		window.dashPrimaryCategories.chosen.sort(this.optionSorter);
	}
};