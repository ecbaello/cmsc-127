app.controller('tableSettings', ['$scope', 'tables', 'tableChanged', function ($scope, tables, tableChanged){
	tables.headers(
			function (response) {
				$scope.headers = response.data.headers;
				$scope.idHeader = response.data.id;
			}
		);

	$scope.headers = {};
	$scope.idHeader = '';
	$scope.types = tables.types;

	$scope.derivedColumnExpr = [];
	$scope.newDToken = {};

	$scope.newColumn = 
	{
		derived: false,
		title: '',
		expression: [],
		kind: '',
		default: ''
	};

	tableChanged.subscribe($scope, function() {
		tables.headers(
			function (response) {
				$scope.headers = response.data.headers;
				$scope.idHeader = response.data.id;
			}
		);
	});

	$scope.transformChip = function ($chip) {
		var token = {
			type: 'operation',
			value: $chip
		};

		$scope.derivedColumnExpr.push(token);

		return null;
	};

	$scope.addDToken = function() {
		var data = $.extend({}, $scope.newDToken);

		data.type = 'field';

		data.derived = $scope.headers[data.header].derived;
		data.derivation = $scope.headers[data.header].select_val;
		
		data.title = $scope.headers[data.header].title;
		$scope.derivedColumnExpr.push(data);
	};

	$scope.addColumn = function() {
		if ($scope.newColumn.derived) {
			$scope.newColumn.expression = $scope.derivedColumnExpr;
		}

		var data = {
			data: angular.toJson($scope.newColumn)
		};

		tables.addColumn(data,
			function(){
				tableChanged.notify();
			},
			function(){
				tableChanged.notify();
			});
	};

	$scope.removeColumn = function (key) {
		var data = {
			header: key
		};
		tables.removeColumn(data,
			function(){
				tableChanged.notify();
			},
			function(){
				tableChanged.notify();
			});
	};
}]);