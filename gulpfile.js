let elixir = require("laravel-elixir"),
	folder = "./assets";

require("laravel-elixir-webpack-official");
require("laravel-elixir-vue-2");

elixir(function (mix) {
	mix.webpack(folder + "/scripts/primary-category.js", folder + "/primary-category.js");
});