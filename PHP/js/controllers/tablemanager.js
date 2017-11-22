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
		title:''
	};

	$scope.new = function() {
		console.log($scope.newItem);

		var data = {};
		data.title = $scope.newItem.title;
		data.prefix = $scope.newItem.prefix;

		data[csrf] = csrfHash;

		var send = {
			type: 'POST',
			url: tablemanagerURL+'/news',
			data: data,
			success: function(resultData) {
				var object = JSON.parse(resultData);
				csrf = object.csrf;
				csrfHash = object.csrf_hash;

				$scope.loadTables();
			},
			error: function() {
				$scope.loadTables();
			}
		};

		$.ajax(send);

	};

	$scope.delete = function(table) {
		console.log($scope.newItem);

		var data = {};
		data.table = table;

		data[csrf] = csrfHash;

		var send = {
			type: 'POST',
			url: tablemanagerURL+'/delete',
			data: data,
			success: function(resultData) {
				var object = JSON.parse(resultData);
				csrf = object.csrf;
				csrfHash = object.csrf_hash;
				
				$scope.loadTables();
			},
			error: function() {
				$scope.loadTables();
			}
		};

		$.ajax(send);

	};
}]);