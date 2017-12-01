app.controller('selector', ['$scope', '$http', 'recognizerUrl', '$mdDialog', function($scope, $http, recognizerUrl, $mdDialog){

	$scope.data = {};
	$scope.recognizing = null;
	$scope.title = '';

	function loadOptions() {
		requestget(
			recognizerUrl+'/data',
			null,
			function (data) {
				$scope.data = data.data;
				$scope.$apply();

				console.log($scope.data);
			},
			function () {

			}
		);
	}

	$scope.recognize = function(data) {
		$scope.recognizing = data;
		$mdDialog.show({
			contentElement: '#recognizerDialog',
			parent: angular.element(document.body),
			targetEvent: $scope.$event,
			clickOutsideToClose: true,
			fullscreen: false
		});
		$scope.closeDialog();
	};

	$scope.recognizeTable = function(data, name) {
		requestpost(
			recognizerUrl+'/identify',
			{ table:data, title:name },
			null,
			function () {
				loadOptions();
			},
			function () {}
		);
	};

	$scope.closeDialog = function() {
		$mdDialog.cancel();
	};

	loadOptions();
}]);