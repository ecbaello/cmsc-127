app.controller('importer', ['$scope', '$http', 'uploadURL', function($scope, $http, uploadURL) {

	$scope.csrf = csrf;
	$scope.csrfHash = csrfHash;

	$scope.loadTables = function () {
		$http.get(uploadURL+'/tablemaps')
			.then(function(response) {
				csrf = response.data.csrf;
				csrfHash = response.data.csrf_hash;

				$scope.csrf = csrf;
				$scope.csrfHash = csrfHash;

				$scope.tableMaps = response.data.tables;
			});
	};

	$scope.loadHeaders = function () {

		var data = {};
		data.table = $scope.tableselect.table_name;

		data[csrf] = csrfHash;

		var send = {
			type: 'POST',
			url: uploadURL+'/headers',
			data: data,
			success: function(resultData) {
				var object = JSON.parse(resultData);
				$scope.headers = object.headers;

				csrf = object.csrf;
				csrfHash = object.csrf_hash;

				$scope.csrf = csrf;
				$scope.csrfHash = csrfHash;

				$scope.$apply();
			},
			error: function() {
				$scope.loadTables();
			}
		};

		$.ajax(send);
	};

	$scope.tableMaps = [];

	$scope.headers = [];

	$scope.processing = false;

	$scope.loading = function() {
		$scope.message = '';
		$scope.processing = true;
	};
	$scope.completed = function(content) {
		$scope.processing = false;

		csrf = content.csrf;
		csrfHash = content.csrf_hash;

		$scope.csrf = csrf;
		$scope.csrfHash = csrfHash;

		if (content.success)
			$scope.message = 'Done: imported successfully.';
		else 
			$scope.message = 'Failed: '+content.success+', '+content.count+' out of '+content.size+' rows imported.';
	};
}]);