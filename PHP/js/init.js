var app = angular.module('app', ['ngMaterial', 'ngMessages'])
	.config(function($mdThemingProvider) {
	  $mdThemingProvider.theme('altTheme')
	    .primaryPalette('green').dark(); // specify primary color, all
	                            // other color intentions will be inherited
	                            // from default
});