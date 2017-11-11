var csrf = '';
var csrfHash = '';

function convertData(input) {
	var data = input;
	
	data = alignTypes(data);

	var modelId = data.id;
	var model = {};
	$.each(data.data, function(index) {
		model[ data.data[index][modelId] ] = data.data[index];
	});
	data.data = model;

	return data;
}

function alignTypes(input) {
	var data = input;
	$.each(data.headers, function(key) {
		if ( data.headers[key].type == "DATE" ) {
			$.each(data.data, function(index) {
				data.data[index][key] = new Date(data.data[index][key]);
			});
		}
	});
	return data;
}

app.factory('tables', ['tableURL', '$http', function(tableURL, $http) {
	var tables = {};

	tables.get = function (next) {
		$http.get(tableURL+'/data')
			.then(function(response) {
				csrf = response.data.csrf;
				csrfHash = response.data.csrf_hash;
				next(response);
			});
	};

	tables.update = function (id, data, fsuccess, ferror) {
		data[csrf] = csrfHash;
		$.ajax({
			type: 'POST',
			url: tableURL+'/update/'+id,
			data: data,
			success: function(resultData) {
				var data = JSON.parse(resultData);
				csrf = data.csrf;
				csrfHash = data.csrf_hash;
				fsuccess(data);
			},
			error: function() {
				ferror();
			}
		});
	};

	tables.add = function (data, fsuccess, ferror) {
		data[csrf] = csrfHash;
		$.ajax({
			type: 'POST',
			url: tableURL+'/add',
			data: data,
			success: function(resultData) {
				var data = JSON.parse(resultData);
				csrf = data.csrf;
				csrfHash = data.csrf_hash;
				fsuccess(data);
			},
			error: function() {
				ferror();
			}
		});
	};

	tables.remove = function (id, data, fsuccess, ferror) {
		data[csrf] = csrfHash;
		$.ajax({
			type: 'POST',
			url: tableURL+'/remove/'+id,
			data: data,
			success: function(resultData) {
				var data = JSON.parse(resultData);
				csrf = data.csrf;
				csrfHash = data.csrf_hash;
				fsuccess(data);
			},
			error: function() {
				ferror();
			}
		});
	};

	tables.search = function (data, fsuccess, ferror) {
		data[csrf] = csrfHash;
		$.ajax({
			type: 'POST',
			url: tableURL+'/search',
			data: data,
			success: function(resultData) {
				var data = JSON.parse(resultData);
				csrf = data.csrf;
				csrfHash = data.csrf_hash;
				fsuccess(data);
			},
			error: function() {
				ferror();
			}
		});
	};

	tables.headers = function (next) {
		$http.get(tableURL+'/headers')
			.then(function(response) {
				csrf = response.data.csrf;
				csrfHash = response.data.csrf_hash;
				next(response);
			});
	};

	tables.addColumn = function (data, fsuccess, ferror) {
		data[csrf] = csrfHash;
		$.ajax({
			type: 'POST',
			url: tableURL+'/addfield',
			data: data,
			success: function(resultData) {
				var data = JSON.parse(resultData);
				csrf = data.csrf;
				csrfHash = data.csrf_hash;
				fsuccess(data);
			},
			error: function() {
				ferror();
			}
		});
	};


	tables.types = 
	{
		TEXT: 'Text',
		TEXTAREA: 'Long Text',
		CHECKBOX: 'Checkbox',
		FLOAT: 'Float',
		NUMBER: 'Integer',
		DATE: 'Date'
	};

	return tables;
}]);


app.controller('database', ['$scope', '$http', '$mdDialog', 'tables', function($scope, $http, $mdDialog, tables){
	// Table Information
	$scope.data = [];
	$scope.idName = '';
	$scope.headers = [];

	$scope.searchOperations = {
		equals: 'is equal to',
		not: 'is not equal to',

		like: 'is like',
		not_like: 'is not like',

		range: 'ranges',
		greater: 'is greater than',
		
		lesser: 'is less than',
	};

	// Table State
	$scope.serverRequesting = false;

	// Loading tables
	$scope.setURL = function(url) {
		$scope.serverRequesting = true;
		$scope.url = url;
		rebuild();
	};
	function rebuild() {
		tables.get(
			function(response) {
	        	var data = convertData(response.data);
				$scope.data = data.data;
				$scope.idName = data.id;
				$scope.headers = data.headers;
				//console.log(data);
				$scope.serverRequesting = false;
	    	});
	}

	// Searching Tables
	$scope.search = 
	{
		condition: 'OR',
		rules: [],
		not: false
	};
	$scope.newSearch = 
	{
		condition: null
	};
	$scope.searchOr = true;
	$scope.addSearch = function(is_and) {
		var search = $scope.newSearch;
		$scope.newSearch = 
		{
			condition: null
		};

		search.header = {
			key: search.header,
			derived: $scope.headers[search.header].derived,
			derivation: $scope.headers[search.header].select_val
		};

		if (is_and) {
			$scope.search
				.rules	// OR
				[$scope.search.rules.length-1] // last item
				.rules	// AND
				.push(search);
		} else {
			var arr = 
			{
				condition: 'AND',
				rules: [],
				not: false
			};
			arr.rules.push(search);
			$scope.search.rules.push(arr);
		}
		
	};
	$scope.removeSearch = function (i, j) {
		
		if ($scope.search.rules[i].rules.length == 1 && j == 0) {
			if ($scope.search.rules.length == 1 && i == 0) {
				$scope.search.rules = [];
				rebuild();
			}
			else delete $scope.search.rules.splice(i,1);
		}
		else $scope.search.rules[i].rules.splice(j,1);
	};
	$scope.goSearch = function () {
		var searchQry = JSON.stringify($scope.search);

		var data = {};
		data.data = searchQry;

		$scope.serverRequesting = true;

		tables.search(
			data,
			function(resultData) {
				var response = convertData(resultData);
				$scope.data = response.data;
				$scope.idName = response.id;
				$scope.headers = response.headers;

				//console.log(data);
				$scope.serverRequesting = false;

				$scope.$apply();
			},
			function() {
				rebuild();
			}
		);
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
			var subdata = $.extend({}, $scope.data[index]);

			$.each($scope.headers, function(key, value) {
				if (value.read_only) {
					delete subdata[key];
				}
			});

			data.data = JSON.stringify(subdata);
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

					$scope.data[id]= alignTypes(dataObj).data[0];
					$scope.serverRequesting = false;
					$scope.$apply();
				},
				function() {
					rebuild();
				}
			);
		} else {

			tables.remove(
				id,
				data,
				function(resultData) {
					var response = resultData;
					
	        		$scope.serverRequesting = false;
	        		delete $scope.data[id];
	        		$scope.$apply();
				},
				function() {
					rebuild();
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
		data.data = JSON.stringify($scope.newItem);

		tables.add(
			data,
			function() {
				rebuild();
			},
			function() {
				rebuild();
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

app.controller('selector', ['$scope', '$http', function($scope, $http){

	$scope.options = {};

	$scope.current = null;

	$scope.redirect = function() {
		window.location.href = $scope.selectorUrl +'/table/'+ $scope.select.link;
	};

	$scope.setURL = function(url) {
		$scope.selectorUrl = url;
		loadOptions();
		
	};

	$scope.setSelected = function(select) {
		//console.log(select);
		$scope.select = $scope.options[select];
		$scope.current = select;
	};

	function loadOptions() {
		$.ajax({
			method: "GET",
			url: $scope.selectorUrl+'/table',
			dataType: "json",
			success: function (data) {
				$scope.options = data.data;
				$scope.setSelected($scope.current);
				$scope.$apply();
			}
		});
		
	}
}]);

app.controller('navi', ['$scope', '$mdSidenav', function($scope,  $mdSidenav){

	$scope.toggleNavi = function() {
		$mdSidenav('navigation')
	      .toggle();
	};
}]);

app.controller('user', ['$scope', 'UserService', function($scope,  UserService){
	$scope.loggedIn = UserService.isLogged;
	$scope.userTitle = UserService.firstName;
	$scope.email = UserService.email;
	$scope.company = UserService.company;
}]);

app.controller('tableSettings', ['$scope', 'tables', function ($scope, tables){
	tables.headers(
			function (response) {
				$scope.headers = response.data.headers;
				console.log($scope.headers);
			}
		);

	$scope.headers = {};
	$scope.types = tables.types;

	$scope.derivedColumnExpr = [];
	$scope.DTokenIsField = true;
	$scope.newDToken = {};

	$scope.newColumn = 
	{
		derived: false,
		title: '',
		expression: [],
		kind: '',
		default: ''
	};

	$scope.addDToken = function(DTokenIsField) {
		var data = $.extend({}, $scope.newDToken);
		if (DTokenIsField) {
			data.type = 'field';

			data.derived = $scope.headers[data.header].derived;
			data.derivation = $scope.headers[data.header].select_val;
			
			data.title = $scope.headers[data.header].title;
		} else {
			data.type = 'operation';
		}
		$scope.derivedColumnExpr.push(data);
	};

	$scope.addColumn = function() {
		if ($scope.newColumn.derived) {
			$scope.newColumn.expression = $scope.derivedColumnExpr;
		};

		var data = {
			data: JSON.stringify($scope.newColumn)
		};

		tables.addColumn(data, function () {}, function () {});
	};
}]);

app.controller('expBuilder', ['$scope', function($scope){
	$scope.expression = [
	{
		type: 'field',
		title: 'Amount',
		header: 'hi'
	},
	{
		type: 'operand',
		value: '*'
	},
	{
		type: 'field',
		title: 'quantity',
		key: 'hilo'
	}

	];
	$scope.newIsField = true;
	$scope.newItem = {};

	$scope.addExpr = function() {
		var data = $.extend({}, $scope.newItem);
		if ($scope.newIsField) {
			data.type = "field";
			data.title = "he";
		} else {
			data.type = "operand";
		}
		$scope.expression.push(data);
	};
}]);

