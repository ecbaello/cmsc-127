// Put here controllers that are loaded always

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

