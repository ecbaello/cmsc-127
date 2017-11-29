// Put here controllers that are loaded always

app.controller('navi', ['$scope', '$mdSidenav', '$mdDialog', 'baseUrl', function($scope,  $mdSidenav, $mdDialog, baseUrl){

	$scope.toggleNavi = function() {
		$mdSidenav('navigation')
	      .toggle();
	};

	$scope.back = function() {
		window.history.back();
	};

	$scope.forward = function() {
		window.history.forward();
	};

	$scope.bookmark = function(bookmark) {
		$scope.closeDialog();
		requestpost(
			baseUrl+'bookmarks/add',
			{
				title: bookmark,
				link: window.location.href
			},
			null,
			function () {
				location.reload();
			},
			function () {
				
			}
		);
	};
	$scope.showBookmarkDialog = function(ev) {
		$mdDialog.show({
			contentElement: '#bookmarkDialog',
			parent: angular.element(document.body),
			targetEvent: ev,
			clickOutsideToClose: true,
			fullscreen: false
		});
	};
	$scope.closeDialog = function() {
		$mdDialog.cancel();
	};
}]);

app.controller('user', ['$scope', 'UserService', function($scope,  UserService){
	$scope.loggedIn = UserService.isLogged;
	$scope.userTitle = UserService.firstName;
	$scope.email = UserService.email;
	$scope.company = UserService.company;
}]);


