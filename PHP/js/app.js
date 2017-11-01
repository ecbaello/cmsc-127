var app = angular.module('app', []);

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


app.controller('database', ['$scope', '$http',  function($scope, $http){
	$scope.data = [];
	$scope.rowClone = [];

	$scope.idName = '';

	$scope.newItem = {};

	$scope.headers = [];

	$scope.editIndex = null;
	$scope.isEdit = true;

	$scope.edit = function (id) {
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
		if ($scope.isEdit) {
			data.data = $scope.data[id];
			data[$scope.csrf] = $scope.csrfHash; 

			$http.post( $scope.url+'/update/'+id,  data)
				.then(
					function(response) {
						$scope.csrf = response.data.csrf;
						$scope.csrfHash = response.data.csrf_hash;
						console.log(response);

						var dataObj = {};
						dataObj.headers = $scope.headers;
						dataObj.data = {};
						dataObj.data[0] = response.data.data;

						$scope.data[id]= alignTypes(dataObj).data[0];
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
		        		console.log(response);

		        		delete $scope.data[id];
		    		}, function(error){
						rebuild();
				    }
		    	);
		}
		$scope.editIndex = null;
	};

	$scope.cancel = function () {
		if ($scope.isEdit) $scope.data[$scope.editIndex] = $scope.rowClone;
		$scope.editIndex = null;
	};

	$scope.add = function () {
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
	};

	rebuild();
	function rebuild() {

		$http.get(window.location.href+'/data')
			.then(function(response) {
	        	var data = convertData(response.data);
				$scope.data = data['data'];
				$scope.idName = data['id'];
				$scope.headers = data['headers'];
				$scope.csrf = data['csrf'];
				$scope.csrfHash = data['csrf_hash'];
	    	});
		
	};

}]);