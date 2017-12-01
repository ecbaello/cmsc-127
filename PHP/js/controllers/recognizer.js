app.controller('recognizer', ['$scope', '$http', 'recognizerUrl', '$mdDialog', function($scope, $http, recognizerUrl, $mdDialog){

	$scope.data = {};
	$scope.recognizing = null;
	$scope.title = '';

	$scope.recognized = [];

	function loadOptions() {
		requestget(
			recognizerUrl+'/data',
			null,
			function (data) {
				$scope.data = data.data;
				$scope.recognized = data.recognized;

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
		
	};

	$scope.unrecognize = function (data) {
		requestpost(
			recognizerUrl+'/unidentify',
			{ table:data},
			null,
			function () {
				loadOptions();
			},
			function () {}
		);
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
		$scope.closeDialog();
	};

	$scope.closeDialog = function() {
		$mdDialog.cancel();
	};

	loadOptions();
}]);