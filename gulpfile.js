let elixir = require("laravel-elixir"),
	folder = "./assets";

require("laravel-elixir-webpack-official");
require("laravel-elixir-vue-2");

elixir(function (mix) {
	mix.webpack(folder + "/scripts/dash-10up-primary-category.js", folder + "/dash-10up-primary-category.js");
});