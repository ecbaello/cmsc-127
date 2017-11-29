app.controller('selector', ['$scope', '$http', function($scope, $http){

	$scope.options = {};

	$scope.current = null;

	$scope.redirect = function() {
		window.location.href = $scope.selectorUrl +'/table/'+ $scope.select.link;
	};

	$scope.setURL = function(url) {
		$scope.selectorUrl = url;
		loadOptions();
	};

	$scope.setSelected = function(select) {
		//console.log(select);
		$scope.select = $scope.options[select];
		$scope.current = select;
	};

	$scope.addCategory = function(category) {
		requestpost(
			$scope.selectorUrl+'/addcategory',
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
			$scope.selectorUrl+'/removecategory',
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
			url: $scope.selectorUrl+'/table',
			dataType: "json",
			success: function (data) {
				$scope.options = data.data;
				$scope.setSelected($scope.current);
				$scope.$apply();

				console.log(data);
			}
		});


		
	}
}]);