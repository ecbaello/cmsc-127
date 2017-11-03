var app = angular.module('app', ['ngMaterial', 'ngMessages'])
	.config(function($mdThemingProvider) {
	  $mdThemingProvider.theme('altTheme')
	    .primaryPalette('green').dark(); // specify primary color, all
	                            // other color intentions will be inherited
	                            // from default
});

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
	$scope.data = [];
	$scope.rowClone = [];

	$scope.idName = '';

	$scope.newItem = {};

	$scope.headers = [];

	$scope.editIndex = -1;
	$scope.isEdit = true;

	$scope.serverRequesting = false;

	$scope.setURL = function(url) {
		$scope.serverRequesting = true;
		$scope.url = url;
		rebuild();
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


	$scope.edit = function (id) {
		$scope.cancel();
		$scope.rowClone = $.extend({}, $scope.data[id]);
		$scope.isEdit = true;
		$scope.editIndex = id;
	};

	$scope.delete = function (id) {
		$scope.isEdit = false;
		$scope.editIndex = id;
	};

	$scope.send = function () {
		var id = $scope.editIndex;
		var data = {};
		$scope.serverRequesting = true;
		if ($scope.isEdit) {
			data.data = $scope.data[id];
			data[$scope.csrf] = $scope.csrfHash; 

			$http.post( $scope.url+'/update/'+id,  data)
				.then(
					function(response) {
						$scope.csrf = response.data.csrf;
						$scope.csrfHash = response.data.csrf_hash;
						//console.log(response);

						var dataObj = {};
						dataObj.headers = $scope.headers;
						dataObj.data = {};
						dataObj.data[0] = response.data.data;

						$scope.data[id]= alignTypes(dataObj).data[0];
						$scope.serverRequesting = false;
		    		}, function(error){
						rebuild();
				    }
		    	);
		} else {
			$http.delete( $scope.url+'/remove/'+id , data)
				.then(
					function(response) {
						$scope.csrf = response.data.csrf;
						$scope.csrfHash = response.data.csrf_hash;
		        		//console.log(response);
		        		$scope.serverRequesting = false;
		        		delete $scope.data[id];
		    		}, function(error){
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

	$scope.add = function () {
		$scope.serverRequesting = true;
		var data = {};
		data.data = $scope.newItem;
		data[$scope.csrf] = $scope.csrfHash; 
		$http.post( $scope.url+'/add' , data)
				.then(
					function(response) {
						rebuild();
		    		}, function(error){
						rebuild();
				    }
		    	);
		$mdDialog.hide();
	};

	function rebuild() {

		$http.get($scope.url+'/data')
			.then(function(response) {
	        	var data = convertData(response.data);
				$scope.data = data['data'];
				$scope.idName = data['id'];
				$scope.headers = data['headers'];
				$scope.csrf = data['csrf'];
				$scope.csrfHash = data['csrf_hash'];
				//console.log(data);
				$scope.serverRequesting = false;
	    	});
		
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