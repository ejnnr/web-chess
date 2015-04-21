var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

var bowerDir = __dirname + '/resources/assets/vendor/';
 
var lessPaths = [
	bowerDir + "bootstrap/less"
];

elixir(function(mix) {
	mix.less('app.less', 'public/css', { paths: lessPaths })
		.scripts([
			'jquery/dist/jquery.min.js',
			'bootstrap/dist/js/bootstrap.min.js'
			], 'public/js/vendor.js', bowerDir);
 
});
