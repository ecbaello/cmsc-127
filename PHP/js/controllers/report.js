

app.controller("LineCtrl", ['$scope', '$interval', function ($scope, $interval) {

    $scope.labels = ["January", "February", "March", "April", "May", "June", "July", "August","September","October","November","December"];

	$scope.year = (new Date()).getFullYear();
    $scope.series = [];
    $scope.colors = [{borderColor:'#f00'},{borderColor:'#0f0'},{borderColor:'#00f'}];

    $scope.data = [0];

    $scope.onClick = function (points, evt) {
        console.log(points, evt);
    };

	$scope.setYear = function(){
		if(!isNaN($scope.year))
			loadData();
	}
	
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

                loadData();
				$scope.$apply();
            }
        });

    }

    function loadData(){
        $.ajax({
            method: "GET",
            url: $scope.selectorUrl+'/getMonthlyExpenses/'+$scope.year,
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
				$scope.$apply();
            }
        });

	}

    $scope.options = {
        title:{
        	display:true,
			text: 'Petty Cash Fund Monthly Expenses'
		},
		legend:{
        	display:true
		}
    };
     //Simulate async data update
    var interval = $interval(function () {
		if($scope.series.length > 0){
			$interval.cancel(interval);
		}
    }, 1000);
	
}]);

app.controller('dateRangeSelector',['$scope',function($scope){

	$scope.fromDate = new Date();
	$scope.toDate = new Date();
	$scope.table = '';
	
	$scope.setURL = function(url) {
        $scope.selectorUrl = url;
    };

	$scope.makeTable = function(subtable){
		
		var fromDate = $scope.fromDate.getFullYear()+'-'+($scope.fromDate.getMonth()+1)+'-'+$scope.fromDate.getDate();
		var toDate = $scope.toDate.getFullYear()+'-'+($scope.toDate.getMonth()+1)+'-'+$scope.toDate.getDate();
		console.log('boom '+$scope.selectorUrl+'/getExpenseTable/'+encodeURI(subtable)+'/0/'+fromDate+'/'+toDate);
		$.ajax({
            method: "GET",
            url: $scope.selectorUrl+'/getExpenseTable/'+encodeURI(subtable)+'/0/'+fromDate+'/'+toDate,
            dataType: "json",
            success: function (data) {
            	$scope.table = data;
				$scope.$apply();
            }
        });
	}


}]);

app.controller('reportTable',['$scope',function($scope){
	
	$scope.setURL = function(url) {
        $scope.selectorUrl = url;
		makeTable();
    };

	function makeTable(){

		$.ajax({
            method: "GET",
            url: $scope.selectorUrl+'/getReports',
            dataType: "json",
            success: function (data) {
            	$scope.table = data;
				$scope.$apply();
            }
        });
	}


}]);

app.controller('pcfReport',['$scope',function($scope){

    $scope.table = '';
    $scope.details = '';
    $scope.inFund = 5000;
    $scope.inThreshold = 3000;

    $scope.setURL = function(url,subtable) {
        $scope.selectorUrl = url;
        makeTable(subtable);
        makePCFDetails(subtable);
    };

    function makeTable(subtable){

        $.ajax({
            method: "GET",
            url: $scope.selectorUrl+'/getExpenseTable/'+encodeURI(subtable)+'/1/',
            dataType: "json",
            success: function (data) {
                $scope.table = data;
                $scope.$apply();
            }
        });
    }

    function makePCFDetails(subtable){
        $.ajax({
            method: "GET",
            url: $scope.selectorUrl+'/administrate/'+encodeURI(subtable),
            dataType: "json",
            success: function (data) {
                $scope.details = data;
                $scope.inFund=data['Allotted Fund'];
                $scope.inThreshold = data['Expense Threshold'];
                $scope.$apply();
            }
        });
    }

    $scope.changeFund = function(subtable){
        if(isNaN($scope.inFund)){
            alert('Invalid Input');
            return;
        }
        $.ajax({
            method: "GET",
            url: $scope.selectorUrl+'/administrate/'+encodeURI(subtable)+'/fund/'+$scope.inFund+'/',
            dataType: "json",
            success: function (data) {
                makePCFDetails(subtable);
                $scope.$apply();
            }
        });
    }

    $scope.changeThreshold = function(subtable,threshold){
        if(isNaN($scope.inThreshold)){
            alert('Invalid Input');
            return;
        }
        $.ajax({
            method: "GET",
            url: $scope.selectorUrl+'/administrate/'+encodeURI(subtable)+'/threshold/'+$scope.inThreshold+'/',
            dataType: "json",
            success: function (data) {
                makePCFDetails(subtable);
                $scope.$apply();
            }
        });

    }

    $scope.replenish = function(subtable){
        $.ajax({
            method: "GET",
            url: $scope.selectorUrl+'/administrate/'+encodeURI(subtable)+'/replenish',
            dataType: "json",
            success: function () {
                makePCFDetails(subtable);
                location.reload();
                $scope.$apply();
            }
        });
    }

}]);