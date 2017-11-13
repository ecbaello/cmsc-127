var app = angular.module('app', ['ngMaterial', 'ngMessages','chart.js'])
	.config(function($mdThemingProvider) {
	  $mdThemingProvider.theme('altTheme')
	    .primaryPalette('grey').dark(); // specify primary color, all
	                            // other color intentions will be inherited
	                            // from default

	    $mdThemingProvider.theme('default')
	    .primaryPalette('green'); // specify primary color, all
	                            // other color intentions will be inherited
	                            // from default
	});

var csrf = '';
var csrfHash = '';