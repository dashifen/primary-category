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

		// our dashPrimaryCategories object is the "state" of this component.
		// it has one property:  chosen.  it's the list of categories that have
		// been chosen for this post by marking their checkboxes.  by putting
		// it here in the component's data, Vue will handle the necessary
		// reactivity when it is altered; see primary-category-behaviors.js
		// for more information on how this object is altered over time.

		return window.dashPrimaryCategories
	},

	methods: {
		isPrimary(option) {

			// if there's only one category in our list of options, then
			// it is the primary by default.  otherwise, we return the
			// value of the isPrimary property of our option object.

			return this.chosen.length === 1 || option.isPrimary;
		},

		rememberChoice(event) {

			// to remember our choice, we want to find the option within
			// the chosen categories that has the event's target's value
			// as it's ID property.  the event's target is our <select>
			// element; so we can get it's value using its options and
			// selectedIndex properties.

			const categoryId = Number(event.target.options[event.target.selectedIndex].value);

			this.chosen.forEach((choice) => {

				// here, we loop over each choice resetting each of their
				// isPrimary properties to false except for the one that
				// matches our chosen chosen category ID.  that one's flag
				// gets set.

				choice.isPrimary = choice.id === categoryId;
			});
		}
	}
};
</script>

<style scoped>
div {
	margin: 10px 0;
}

label {
	font-weight: bold;
	display: block;
}

select {
	width: 100%;
}
</style>