app.controller('database', ['$scope', '$http', '$mdDialog', 'tables', 'tableChanged', function($scope, $http, $mdDialog, tables, tableChanged){

	// Table Information
	$scope.data = [];
	$scope.idName = '';
	$scope.headers = [];

	$scope.fetchableCount = 0;

	$scope.filterOperations = {
		equals: 'is equal to',
		not: 'is not equal to',

		like: 'is like',
		not_like: 'is not like',

		range: 'ranges',
		greater: 'is greater than',
		
		lesser: 'is less than',
	};

	tableChanged.subscribe($scope, function() {
		$scope.filter.rules = [];
		$scope.rebuild(true);
	});

	// Table State
	$scope.serverRequesting = true;


	// Loading tables
	$scope.rebuild = function(withHeaders) {
		var data = {};

		if ($scope.filter.rules.length > 0) {
			var filterQry = angular.toJson($scope.filter);

			data.filter = filterQry;

			filtering = true;
		} 

		$scope.serverRequesting = true;

		var gets = {};

		if (withHeaders) gets.headers = 1;

		if ($scope.sortHeader !== null) {
			gets.orderby = $scope.sortHeader;
			gets.order = $scope.isAscending ? 'ASC' : 'DESC';
		}

		gets.page = $scope.page;
		gets.limit = $scope.limit;

		$scope.editIndex = -1;

		tables.get(
			data,
			function(resultData) {
				var response = convertData(resultData);
				$scope.data = response.data;

				if (withHeaders) {
					$scope.idName = response.id;
					$scope.headers = response.headers;
				}

				$scope.fetchableCount = response.count;

				//console.log(data);
				$scope.serverRequesting = false;


				$scope.$apply();


			},
			function() {
				console.log('Error.');
			},
			gets
		);
	};

	$scope.nextpage = 0;
	$scope.onJump = false;
	$scope.jump = function() {
		if ($scope.onJump) {
			if ($scope.page != $scope.nextpage-1) {
				$scope.page = $scope.nextpage-1;
				var max = Math.ceil($scope.fetchableCount/$scope.limit);
				if ($scope.page > max-1) $scope.page = max-1;
				if ($scope.page <= 0) $scope.page = 0;
				$scope.rebuild();
			}
			$scope.onJump = false;
			
		} else {
			$scope.onJump = true;
			console.log($scope.page);
			$scope.nextpage = $scope.page+1;
			console.log($scope.nextpage);
		}
	};

	$scope.isAscending = true;
	$scope.sortHeader = null;
	$scope.sort = function(header) {
		if ($scope.sortHeader == header) $scope.isAscending = !$scope.isAscending;
		else {
			$scope.sortHeader = header;
			$scope.isAscending = true;
		}
		$scope.rebuild(false);
	};

	// filtering Tables
	var filtering = false;
	$scope.filter = 
	{
		condition: 'OR',
		rules: [],
		not: false
	};
	$scope.newFilter = 
	{
		condition: null
	};
	$scope.filterOr = true;
	$scope.addFilter = function(is_and) {
		var filter = $scope.newFilter;
		$scope.newFilter = 
		{
			condition: null
		};

		filter.header = {
			key: filter.header,
			derived: $scope.headers[filter.header].derived,
			derivation: $scope.headers[filter.header].select_val
		};

		if (is_and) {
			$scope.filter
				.rules	// OR
				[$scope.filter.rules.length-1] // last item
				.rules	// AND
				.push(filter);
		} else {
			var arr = 
			{
				condition: 'AND',
				rules: [],
				not: false
			};
			arr.rules.push(filter);
			$scope.filter.rules.push(arr);
		}
	};
	$scope.removeFilter = function (i, j) {
		if ($scope.filter.rules[i].rules.length == 1 && j == 0) {
			if ($scope.filter.rules.length == 1 && i == 0) {
				$scope.filter.rules = [];
				$scope.rebuild(false);
			}
			else delete $scope.filter.rules.splice(i,1);
		}
		else $scope.filter.rules[i].rules.splice(j,1);
	};

	$scope.limitOptions = [5, 10, 20, 30, 50 ,100, 150];
	$scope.limit = 10;
	$scope.page = 0;
	$scope.navigate = function (forward) {
		var changed = false;
		
		if (forward) {
			if ( $scope.page + 1 < Math.ceil($scope.fetchableCount/$scope.limit) ) {
				$scope.page++;
				changed = true;
			}
		} else {
			if ( $scope.page > 0 ) {
				$scope.page--;
				changed = true;
			}
		}

		if (changed) $scope.rebuild(false);
	};

	// Editing Tables
	$scope.editIndex = -1;
	$scope.isEdit = true;
	$scope.rowClone = [];



	$scope.edit = function (index) {
		$scope.cancel();
		$scope.rowClone = $.extend({}, $scope.data[index]);
		$scope.isEdit = true;
		$scope.editIndex = index;
	};

	$scope.delete = function (index) {
		$scope.isEdit = false;
		$scope.editIndex = index;
	};

	$scope.send = function () {
		var index = $scope.editIndex;
		var id = $scope.data[index][$scope.idName];
		var data = {};

		$scope.serverRequesting = true;
		if ($scope.isEdit) {
			var subdata = {};

			$.each($scope.headers, function(key, value) {
				if (!value.read_only) {
					subdata[key] = $scope.data[index][key];
				}
			});

			data.data = angular.toJson(subdata);

			console.log(data);

			tables.update(
				id,
				data,
				function(resultData) {
					var response = resultData;

					var dataObj = {};
					dataObj.headers = $scope.headers;
					dataObj.data = {};
					dataObj.data[0] = response.data;

					$scope.data[index]= alignTypes(dataObj).data[0];
					$scope.serverRequesting = false;
					$scope.$apply();
				},
				function() {
					$scope.rebuild(true);
				}
			);
		} else {

			tables.remove(
				id,
				data,
				function(resultData) {
					$scope.rebuild(false);
				},
				function() {
					$scope.rebuild(true);
				}
			);

		}
		$scope.editIndex = -1;
	};

	$scope.cancel = function () {
		if ($scope.editIndex != -1){
			if ($scope.isEdit) $scope.data[$scope.editIndex] = $scope.rowClone;
			$scope.editIndex = -1;
		}
	};


	// Adding to Tables
	$scope.newItem = {};
	$scope.add = function () {
		$scope.serverRequesting = true;
		var data = {};
		data.data = angular.toJson($scope.newItem);

		tables.add(
			data,
			function() {
				$scope.rebuild(false);
			},
			function() {
				$scope.rebuild(true);
			}
		);
		$mdDialog.hide();
	};

	$scope.showAddDialog = function(ev) {
		$mdDialog.show({
			contentElement: '#addDialog',
			parent: angular.element(document.body),
			targetEvent: ev,
			clickOutsideToClose: true,
			fullscreen: true
		});
	};

	$scope.closeDialog = function() {
		$mdDialog.cancel();
	};
}]);