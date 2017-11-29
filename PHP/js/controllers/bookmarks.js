app.controller('bookmarks', ['$scope', 'baseUrl', '$http', function($scope, baseUrl, $http){

	$scope.bookmarks = [];

	$scope.loadBookmarks = function() {

		requestget(
			baseUrl+'bookmarks/data',
			null,
			function (resultData) {
				$scope.bookmarks = resultData.data;
				console.log(resultData.data);
				$scope.$apply();
			},
			function() {
				
			}
		);
	};

	$scope.removeBookmark = function(name) {

		requestpost(
			baseUrl+'bookmarks/remove',
			{title: name},
			null,
			function (resultData) {
				if (resultData.success) location.reload();
			},
			function() {

			}
		);
	};
}]);

