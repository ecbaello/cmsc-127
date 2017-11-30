app.controller('selector', ['$scope', '$http', 'selectorUrl', 'selectorSelection', function($scope, $http, selectorUrl, selectorSelection){

	$scope.options = {};

	$scope.selection = selectorSelection;

	$scope.menuUrl = selectorUrl;

	$scope.redirect = function(url) {
		window.location.href = selectorUrl +'/table/'+ url;
	};

	$scope.addCategory = function(category) {
		requestpost(
			selectorUrl+'/addcategory',
			{title: category},
			null,
			function(data) {
				loadOptions();
				$scope.$apply();
			},
			function() {
				
			}
			);
	};

	$scope.removeCategory = function(category) {
		requestpost(
			selectorUrl+'/removecategory',
			{title: category},
			null,
			function(data) {
				loadOptions();
				$scope.$apply();
			},
			function() {
				
			}
			);
	};

	function loadOptions() {
		$.ajax({
			method: "GET",
			url: selectorUrl+'/table',
			dataType: "json",
			success: function (data) {
				$scope.options = data.data;
				$scope.$apply();

				console.log(data);
			}
		});
	}

	loadOptions();
}]);