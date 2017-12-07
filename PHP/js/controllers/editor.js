app.controller('editor', ['$scope', '$http', 'editorUrl', 'editInitData','$mdDialog',
	function($scope, $http, editorUrl, editInitData, $mdDialog){

		$scope.headers = {};
		$scope.editItem = JSON.parse(editInitData);

		$scope.loadHeaders = function() {
			requestget(editorUrl, {action: 'headers'},
				function(data) {
					$scope.headers = data.headers;
					$scope.$apply();
				},
				function() {
					
				});
		};

		$scope.loadHeaders();

		$scope.update = function () {
			var subdata = {};

			angular.forEach($scope.headers, function(value, key) {
				if (!value.read_only) {
					subdata[key] = $scope.editItem[key];
				}
			});

			var data = { data: angular.toJson(subdata) };
			requestpost(editorUrl, data, {action: 'update'},
				function(response) {
					$scope.editItem = response.data;
					$scope.$apply();
				},
				function() {}
				);
		};

		$scope.delete = function () {
			var subdata = {};

			angular.forEach($scope.headers, function(value, key) {
				if (!value.read_only) {
					subdata[key] = $scope.editItem[key];
				}
			});

			var data = { data: angular.toJson(subdata) };
			requestget(editorUrl, {action: 'remove'},
				function(data) {
					if (data.success) {
						window.history.back();
					}
				},
				function() {}
				);
		};

	}
]);