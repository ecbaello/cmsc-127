var csrf = '';
var csrfHash = '';

function convertData(input) {
	var data = input;
	
	//data = alignTypes(data);

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

app.filter('page', function() {
  return function(input) {
    return input === 0 ? 1 : Math.ceil(input);
  };
});

app.factory('tables', ['tableURL', '$http', function(tableURL, $http) {
	var tables = {};

	tables.get = function (options, fsuccess, ferror, gets) {
		var request = {
			success: function(resultData) {
				var data = JSON.parse(resultData);
				csrf = data.csrf;
				csrfHash = data.csrf_hash;
				fsuccess(data);
			},
			error: function() {
				ferror();
			}
		};

		if (!$.isEmptyObject(options)) {
			options[csrf] = csrfHash;

			request.data = options;
			request.type = 'POST';

			request.url = tableURL+'/data'+encodeObject(gets);
		} else {
			request.type = 'GET';
			request.data = gets;
			request.url = tableURL+'/data';
		}

		$.ajax(request);
	};

	var encodeObject = function (getopts) {
		var append = '';
		if (getopts !== null && !$.isEmptyObject(getopts)) {
			var begin = '?';
			$.each(getopts, function(prop) {
				append += begin;
				if (begin == '?') begin = '&';
				append += encodeURIComponent(prop)+'='+encodeURIComponent(getopts[prop]);
			});
		}
		return append;
	};

	var reqpost = function (link, id, data, fsuccess, ferror) {
		
		data[csrf] = csrfHash;
		var request = {
			type: 'POST',
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
		};

		request.url = tableURL+'/'+link;

		if (id !== null) {
			request.url += '/'+id;
		}

		$.ajax(request);
	};

	tables.update = function (id, data, fsuccess, ferror) {
		reqpost('update', id, data, fsuccess, ferror);
	};

	tables.add = function (data, fsuccess, ferror) {
		reqpost('add', null, data, fsuccess, ferror);
	};

	tables.remove = function (id, data, fsuccess, ferror) {
		reqpost('remove', id, data, fsuccess, ferror);
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
		reqpost('addfield', null, data, fsuccess, ferror);
	};

	tables.removeColumn = function (data, fsuccess, ferror) {
		reqpost('removefield', null, data, fsuccess, ferror);
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

app.factory('tableChanged', function($rootScope) {
    return {
        subscribe: function(scope, callback) {
            var handler = $rootScope.$on('table-changed-event', callback);
            scope.$on('$destroy', handler);
        },

        notify: function() {
            $rootScope.$emit('table-changed-event');
        }
    };
});


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
				// ERROR
			},
			gets
		);
	};

	// table customize ui
	$scope.tableView = {
		compact: false,
		bordered: true,
		striped: true
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

app.controller("LineCtrl", ['$scope', '$timeout', function ($scope, $timeout) {

    $scope.labels = ["January", "February", "March", "April", "May", "June", "July", "August","September","October","November","December"];


    $scope.series = [];
    $scope.colors = [{borderColor:'#f00'},{borderColor:'#0f0'},{borderColor:'#00f'}];

    $scope.data = [0];

    $scope.onClick = function (points, evt) {
        console.log(points, evt);
    };

    $scope.setURL = function(url) {
        $scope.selectorUrl = url;
        loadSeries();
    };

    function loadSeries() {
        $.ajax({
            method: "GET",
            url: $scope.selectorUrl+'/table',
            dataType: "json",
            success: function (data) {
            	var log=[];
            	angular.forEach(data.data, function(value,key){
					this.push(value.title);
				},log);
            	$scope.series=log;

                $scope.series.forEach(function(value){
                	loadData(value);
				});
            }
        });

    }

    function loadData(table){
        $.ajax({
            method: "GET",
            url: $scope.selectorUrl+'/getMonthlyExpenses',
            dataType: "json",
            success: function (data) {
                var log=[];

                angular.forEach(data, function(value,key){
                	var log2=[];
                    angular.forEach(value, function(value,key){
						//console.log(value);
						this.push(value);
					},log2);
                    this.push(log2);
                },log);

               	$scope.data = log;
            }
        });

	}

    $scope.options = {
        title:{
        	display:true,
			text: 'Petty Cash Fund Expenses'
		},
		legend:{
        	display:true
		}
    };
    // Simulate async data update
    $timeout(function () {
    }, 500);
}]);

