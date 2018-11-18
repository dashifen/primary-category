<template>
	<div id="primary-category-container" class="hide-if-no-js">
		<label for="primary-category-select">Select Primary Category</label>
		<select id="primary-category-select" name="primary-category" @change="rememberChoice">
			<option value="">None</option>
			<option v-for="option in chosen" :selected="isPrimary(option)" :value="option.id">{{ option.name }}</option>
		</select>
	</div>
</template>

<script>
export default {
	name: "primary-category",

	data() {

		// our dashPrimaryCategories object is the "state" of this
		// component.  it has two properties:  chosen and choice.  the
		// first is the list of chosen categories, and the second one
		// is so we can remember the choice that we make on-screen
		// until it's submitted and saved in the database.

		return window.dashPrimaryCategories
	},

	updated() {
		if (this.choice) {

			// if our component has updated but we've already made a
			// choice, then we need to see if the choice we made is still
			// in our list of options.  if so, we mark it primary


		}
	},

	methods: {
		isPrimary(option) {

			// if there's only one category in our list of options, then
			// it is the primary by default.  otherwise, we return the
			// value of the isPrimary property of our option object.

			return this.chosen.length === 1 || option.isPrimary;
		},

		rememberChoice(event) {

			// to set our choice, we want to mutate the state of our
			// component.  thus, we edit the actual state object here.
			// had we been using Vuex, we'd use this.$store.commit
			// but that's probably overkill for this plugin's purpose.

			const categoryId = Number(event.target.options[event.target.selectedIndex].value);

			this.chosen.forEach((choice) => {
				choice.isPrimary = choice.id === categoryId;
			});
		}
	}
};
</script>