app.controller("tablemanager", ['$scope', '$http', 'tablemanagerURL', function ($scope, $http, tablemanagerURL) {
	$scope.loadTables = function() {
		$http.get(tablemanagerURL+'/data')
			.then(function(response) {
				csrf = response.data.csrf;
				csrfHash = response.data.csrf_hash;

				$scope.customTables = response.data.data;

				console.log($scope.customTables);
			});
	};

	$scope.customTables = [];
	$scope.newItem = {
		prefix:'',
		title:'',
		array: 0
	};

	$scope.new = function() {

		var data = {};
		data.title = $scope.newItem.title;
		data.prefix = $scope.newItem.prefix;
		data.array = $scope.newItem.array;

		requestpost(
			tablemanagerURL+'/new',
			data,
			function(resultData) {
				var object = JSON.parse(resultData);

				$scope.loadTables();
			}, function() {
				$scope.loadTables();
			});
	};

	$scope.delete = function(table) {
		console.log($scope.newItem);

		var data = {};
		data.table = table;

		requestpost(
			tablemanagerURL+'/delete',
			data,
			function(resultData) {
				var object = JSON.parse(resultData);
				csrf = object.csrf;
				csrfHash = object.csrf_hash;

				$scope.loadTables();
			}, function() {
				$scope.loadTables();
			});

	};
}]);