app.controller('selector', ['$scope', '$http', 'selectorUrl', 'selectorSelection','$mdDialog', function($scope, $http, selectorUrl, selectorSelection, $mdDialog){

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

	$scope.rename = function(key) {
		$scope.renaming = $scope.options[key].title;
		$scope.newName = $scope.renaming;
		$mdDialog.show({
			contentElement: '#renameCategoryDialog',
			parent: angular.element(document.body),
			targetEvent: $scope.$event,
			clickOutsideToClose: true,
			fullscreen: false
		});
	};

	$scope.renaming = null;
	$scope.newName = '';

	$scope.renameCategory = function(category, pn) {
		requestpost(
			selectorUrl+'/renamecategory',
			{title: category, name: pn},
			null,
			function(data) {
				loadOptions();
				$scope.$apply();
			},
			function() {
				
			}
			);
		$scope.closeDialog();
	};

	$scope.closeDialog = function() {
		$mdDialog.cancel();
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