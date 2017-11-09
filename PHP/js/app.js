

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


app.controller('database', ['$scope', '$http', '$mdDialog', function($scope, $http, $mdDialog){
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
		$http.get($scope.url+'/data')
			.then(function(response) {
	        	var data = convertData(response.data);
				$scope.data = data.data;
				$scope.idName = data.id;
				$scope.headers = data.headers;
				$scope.csrf = data.csrf;
				$scope.csrfHash = data.csrf_hash;
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
			if ($scope.search.rules.length == 1 && i == 0) $scope.search.rules = [];
			else delete $scope.search.rules.splice(i,1);
		}
		else $scope.search.rules[i].rules.splice(j,1);
	};
	$scope.goSearch = function () {
		var searchQry = JSON.stringify($scope.search);

		var data = {};
		data.data = searchQry;
		data[$scope.csrf] = $scope.csrfHash;

		$scope.serverRequesting = true;

		$.ajax({
			type: 'POST',
			url: $scope.url+'/search',
			data: data,
			success: function(resultData) {
				var response = convertData(JSON.parse(resultData));
				$scope.data = response.data;
				$scope.idName = response.id;
				$scope.headers = response.headers;

				$scope.csrf = response.csrf;
				$scope.csrfHash = response.csrf_hash;
				//console.log(data);
				$scope.serverRequesting = false;

				$scope.$apply();
			},
			error: function() {
				rebuild();
			}
		});
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

		data[$scope.csrf] = $scope.csrfHash;
		$scope.serverRequesting = true;
		if ($scope.isEdit) {
			var subdata = $.extend({}, $scope.data[index]);

			$.each($scope.headers, function(key, value) {
				if (value.read_only) {
					delete subdata[key];
				}
			});

			data.data = JSON.stringify(subdata);

			$.ajax({
				type: 'POST',
				url: $scope.url+'/update/'+id,
				data: data,
				success: function(resultData) {
					var response = JSON.parse(resultData);
					$scope.csrf = response.csrf;
					$scope.csrfHash = response.csrf_hash;

					var dataObj = {};
					dataObj.headers = $scope.headers;
					dataObj.data = {};
					dataObj.data[0] = response.data;

					$scope.data[id]= alignTypes(dataObj).data[0];
					$scope.serverRequesting = false;
					$scope.$apply();
				},
				error: function() {
					rebuild();
				}
			});
		} else {

			$.ajax({
				type: 'POST',
				url: $scope.url+'/remove/'+id,
				data: data,
				success: function(resultData) {
					var response = JSON.parse(resultData);
					$scope.csrf = response.csrf;
					$scope.csrfHash = response.csrf_hash;
	        		//console.log(response);
	        		$scope.serverRequesting = false;
	        		delete $scope.data[id];
	        		$scope.$apply();
				},
				error: function() {
					rebuild();
				}
			});

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
		data[$scope.csrf] = $scope.csrfHash; 
		$.ajax({
			type: 'POST',
			url: $scope.url+'/add',
			data: data,
			success: function() {
				rebuild();
			},
			error: function() {
				rebuild();
			}
		});
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

app.controller('tables', ['$scope', '$http', function($scope, $http){

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

app.controller('expBuilder', ['$scope', function($scope){
	$scope.expression = [
	{
		type: 'field',
		title: 'Hello',
		key: 'hi'
	},
	{
		type: 'operand',
		value: '+'
	},
	{
		type: 'field',
		title: 'World',
		key: 'hilo'
	}

	];
	$scope.newIsField = true;
	$scope.newItem = {};
}]);

