

app.controller("LineCtrl", ['$scope', '$interval', function ($scope, $interval) {

    $scope.labels = ["January", "February", "March", "April", "May", "June", "July", "August","September","October","November","December"];

	$scope.year = (new Date()).getFullYear();
    $scope.series = [];

    $scope.data = [0];

    $scope.onClick = function (points, evt) {
        console.log(points, evt);
    };

	$scope.setYear = function(){
		if(!isNaN($scope.year))
			loadSeries();
	}
	
    $scope.setURL = function(url) {
        $scope.selectorUrl = url;
        loadSeries();
    };

    function loadSeries() {
        $.ajax({
            method: "GET",
            url: $scope.selectorUrl+'/getMonthlyExpenses/'+$scope.year,
            dataType: "json",
            success: function (data) {
				$scope.series=[];
				var log=[];
                angular.forEach(data, function(value,key){
                	var log2=[];
					$scope.series.push(key);
                    angular.forEach(value, function(value){
						this.push(value);
					},log2);
                    this.push(log2);
                },log);
				
            	$scope.data=log;
				$scope.$apply();
            }
        });

    }

    $scope.options = {
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

app.controller('tableSelector',['$scope',function($scope){
	
	$scope.current = '';
	
	$scope.setURL = function(url) {
        $scope.selectorUrl = url;
		makeSelector();
    };

	function makeSelector(){

		$.ajax({
            method: "GET",
            url: $scope.selectorUrl+'/getModelNames',
            dataType: "json",
            success: function (data) {
            	$scope.data = data;
				$scope.$apply();
            }
        });
	}
	
	$scope.setSelected = function(select) {
		$scope.select = select;
	};
	
	$scope.redirect = function() {
		window.location.href = $scope.selectorUrl +'/table/'+ encodeURI($scope.select);
	};


}]);

app.controller('reportTable',['$scope',function($scope){
	
	$scope.fields = [];
	$scope.model = '';
	$scope.options='add';
	$scope.fromDate = new Date();
	$scope.toDate = new Date();
	$scope.customTable = '';
	
	$scope.setURL = function(url,model) {
        $scope.selectorUrl = url;
		setModel(model);
    };
	
	$scope.changeFields = function(){
		var fromDate = $scope.fromDate.getFullYear()+'-'+($scope.fromDate.getMonth()+1)+'-'+$scope.fromDate.getDate();
		var toDate = $scope.toDate.getFullYear()+'-'+($scope.toDate.getMonth()+1)+'-'+$scope.toDate.getDate();
		$.ajax({
            method: "GET",
            url: $scope.selectorUrl+'/table/'+encodeURI($scope.model)+'/fields',
			data: {'data':$scope.fields},
            dataType: "json",
            success: function (data) {
				loadFields();
            	//window.location.reload();
            }
        });
	}
	
	$scope.makeCustomTable = function(){
		
		var fromDate = $scope.fromDate.getFullYear()+'-'+($scope.fromDate.getMonth()+1)+'-'+$scope.fromDate.getDate();
		var toDate = $scope.toDate.getFullYear()+'-'+($scope.toDate.getMonth()+1)+'-'+$scope.toDate.getDate();
		$.ajax({
            method: "GET",
            url: $scope.selectorUrl+'/table/'+encodeURI($scope.model)+'/custom/'+fromDate+'/'+toDate,
            dataType: "json",
            success: function (data) {
            	$scope.customTable = data;
				$scope.$apply();
            }
        });
	}

	function makeTable(){

		$.ajax({
            method: "GET",
            url: $scope.selectorUrl+'/table/'+encodeURI($scope.model)+'/reports/',
            dataType: "json",
            success: function (data) {
            	$scope.table = data;
				$scope.$apply();
            }
        });
	}
	
	function setModel(model){
		$scope.model = model;
		loadFields();
	}
	
	function loadFields(){
		$.ajax({
            method: "GET",
            url: $scope.selectorUrl+'/table/'+encodeURI($scope.model)+'/fields/',
            dataType: "json",
            success: function (data) {
				$scope.fields = [];
				angular.forEach(data,function(value){
					var temp = {};
					temp['field']=value['field'];
					temp['name']=value['name'];
					temp['option']=value['option'];
					$scope.fields.push(temp);
				});
				makeTable();
				$scope.makeCustomTable();
				$scope.$apply();
            }
        });
	}


}]);

app.controller('reportSettings',['$scope',function($scope){
	
	$scope.unTables = [];
	$scope.reportTables=[];
	$scope.customTable = '';
	
	$scope.tableSelect = '';
	$scope.fieldSelect = '';
	
	$scope.existsField = false;
	
	$scope.setURL = function(url,model) {
        $scope.selectorUrl = url;
		init();
    };
	
	function init(){
		loadUnTables();
		loadReportTables();
	}

	function loadUnTables(){
		$.ajax({
            method: "GET",
            url: $scope.selectorUrl+'/settings/',
            dataType: "json",
            success: function (data) {
				var fields = data;
				$scope.unTables = fields;
				$scope.apply;
            }
        });
	}
	
	function loadReportTables(){
		$.ajax({
            method: "GET",
            url: $scope.selectorUrl+'/settings/map/',
            dataType: "json",
            success: function (data) {
				$scope.reportTables = data;
				$scope.apply;
            }
        });
		
	}

	$scope.addTable=function(){
		if($scope.tableSelect === '' || $scope.fieldSelect ===''){
			alert('Please select a table and a field');
			return;
		}
		var output = $scope.unTables[$scope.tableSelect];
		$.ajax({
            method: "GET",
            url: $scope.selectorUrl+'/settings/add/'+encodeURI(output.table)+'/'+encodeURI(output.fields[$scope.fieldSelect].field),
            success: function () {
            	window.location.reload();
            }
        });
	}
	
	$scope.changeTable=function(){
		if($scope.tableSelect === '' || $scope.fieldSelect ===''){
			alert('Please select a table and a field');
			return;
		}
		var output = $scope.reportTables[$scope.tableSelect];
		$.ajax({
            method: "GET",
            url: $scope.selectorUrl+'/settings/change/'+encodeURI(output.table)+'/'+encodeURI(output.fields[$scope.fieldSelect].field),
            success: function () {
            	window.location.reload();
            }
        });
	}
	
	$scope.removeTable=function(){
		if($scope.tableSelect === ''){
			alert('Please select a table');
			return;
		}
		var output = $scope.reportTables[$scope.tableSelect];
		$.ajax({
            method: "GET",
            url: $scope.selectorUrl+'/settings/remove/'+encodeURI(output.table),
            success: function () {
            	window.location.reload();
            }
        });
	}
	
	$scope.changeTab=function(){
		$scope.tableSelect = '';
		$scope.fieldSelect = '';
	}
	
	$scope.selected = function(){
		$scope.fieldSelect='';
		if(typeof $scope.unTables[$scope.tableSelect] === "undefined")
			$scope.existsField=false;
		$scope.existsField = $scope.unTables[$scope.tableSelect].fields.length > 0 ? true:false;
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