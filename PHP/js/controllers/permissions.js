app.controller("permissions", ['$scope', '$http', 'permissionsURL', function ($scope, $http, permissionsURL) {
	$scope.loadPermissions = function() {
		$http.get(permissionsURL+'/data')
			.then(function(response) {
				csrf = response.data.csrf;
				csrfHash = response.data.csrf_hash;

				$scope.permissionTable = response.data.data;
				$scope.groups = response.data.groups;
				$scope.models = response.data.models;

				console.log($scope.permissionTable);
			});
	};
	$scope.groups = [];
	$scope.models = [];
	$scope.permissionTable = {};

	$scope.changed = function(table, gid, permission) {
		if ($scope.permissionTable[table] == undefined) {
			$scope.permissionTable[table] = {};
		}

		// console.log('@table['+table+']@group['+gid+'] = '+$scope.permissionTable[table][gid]);
		// console.log($scope.permissionTable);
		
		$scope.permissionTable[table][gid] = permission;

		var data = {};
		data.table = table;
		data.group = gid;
		data.permission = $scope.permissionTable[table][gid];

		data[csrf] = csrfHash;

		var send = {
			type: 'POST',
			url: permissionsURL+'/set',
			data: data,
			success: function(resultData) {
				var object = JSON.parse(resultData);
				$scope.permissionTable[table][gid] = object.permission;

				csrf = object.csrf;
				csrfHash = object.csrf_hash;

				$scope.$apply();
			},
			error: function() {
				$scope.loadPermissions();
			}
		};

		$.ajax(send);

	};
}]);