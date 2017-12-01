app.controller('tableSettings', ['$scope', 'tables', 'tableChanged', '$mdDialog', function ($scope, tables, tableChanged, $mdDialog){
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

	$scope.isPrivate = false;
	$scope.checkPrivacy = function () {
		tables.getprivacy(
			function (resultData) {
				$scope.isPrivate = resultData.private;
				$scope.$apply();
			}, 
			function () {});
	};
	$scope.modifyPrivacy = function (value) {
		tables.setprivacy(
			value,
			function () {
				
			}, 
			function () {});
	};

	$scope.download = function() {
		window.location.href = tables.downloadUrl();
	};

	$scope.addDToken = function() {
		var data = angular.extend({}, $scope.newDToken);

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
		tables.removeColumn(key,
			function(){
				tableChanged.notify();
			},
			function(){
				tableChanged.notify();
			});
		$scope.closeDialog();
	};

	$scope.editColumn = function(key) {
		$scope.columnNewName = $scope.headers[key].title;
		$scope.editing = key;
		$mdDialog.show({
			contentElement: '#renameColumnDialog',
			parent: angular.element(document.body),
			targetEvent: $scope.$event,
			clickOutsideToClose: true,
			fullscreen: false
		});
	};

	$scope.editing = null;
	$scope.columnNewName = '';
	$scope.renameColumn = function (key, name) {
		tables.renameColumn(key, name,
			function(){
				tableChanged.notify();
			},
			function(){
				tableChanged.notify();
			});
	};

	$scope.closeDialog = function() {
		$mdDialog.cancel();
	};
}]);