var app = angular.module('app', ['ngMaterial', 'ngMessages','chart.js', 'ngUpload','ngTableToCsv'])

	.config(function($mdThemingProvider) {
	  $mdThemingProvider.theme('altTheme')
	    .primaryPalette('grey').dark(); // specify primary color, all
	                            // other color intentions will be inherited
	                            // from default

	    $mdThemingProvider.theme('default')
	    .primaryPalette('green'); // specify primary color, all
	                            // other color intentions will be inherited
	                            // from default
	}).run(function($rootScope, $mdColors) {
    	$rootScope.$mdColors = $mdColors;
	});

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

var generateError = function (error_code, message) {
	alert('Error '+error_code+': '+message);
};

var loadCSRF = function(obj) {
	csrf = obj.csrf;
	csrfHash = obj.csrf_hash;
};

var requestAJAX = function (link, data, encodeable, fsuccess, ferror) {
	
	var request = {
		url: link,
		success: function(resultData) {
			var data = JSON.parse(resultData);
			loadCSRF(data);

			if(data.success === false){
				alert('Error: '+(typeof data.error_message!=='undefined' ? data.error_message:''));
			}
			
			fsuccess(data);
		},
		error: function(error) {
			alert('Something went wrong.');
			ferror();
		}
	};

	if (data && !$.isEmptyObject(data)) {

		data[csrf] = csrfHash;

		request.type = 'POST';
		request.data = data;

		request.url = request.url+(encodeable?encodeObject(encodeable):'');

		
	} else {
		request.type = 'GET';

		if (encodeable)
			request.data = encodeable;
	}

	$.ajax(request);
};

var requestpost = function (link, data, encodeable, fsuccess, ferror) {
	requestAJAX(link, data, encodeable, fsuccess, ferror);
};

var requestget = function (link, encodeable, fsuccess, ferror) {
	requestAJAX(link, null, encodeable, fsuccess, ferror);
};

app.filter('page', function() {
  return function(input) {
    return input === 0 ? 1 : Math.ceil(input);
  };
});

app.factory('tables', ['tableURL', '$http', function(tableURL, $http) {
	var tables = {};

	tables.get = function (options, fsuccess, ferror, gets) {
		requestAJAX(tableURL+'/data', options, gets, fsuccess, ferror);
	};

	

	var reqpost = function (link, id, data, fsuccess, ferror) {

		var href = tableURL+'/'+link;

		if (id !== null) {
			href += '/'+id;
		}

		requestpost(href, data, null, fsuccess, ferror);
	};

	tables.update = function (id, data, fsuccess, ferror) {
		reqpost('update', id, data, fsuccess, ferror);
	};

	tables.add = function (data, fsuccess, ferror) {
		reqpost('add', null, data, fsuccess, ferror);
	};

	tables.saveFilter = function (data, fsuccess, ferror) {
		reqpost('filters/add', null, data, fsuccess, ferror);
	};

	tables.deleteFilter = function (id, fresponse) {
		$http.get(tableURL+'/filters/remove/'+id)
			.then(function(response) {
				csrf = response.data.csrf;
				csrfHash = response.data.csrf_hash;
				fresponse(response.data);
			});
	};

	tables.filters = function (next) {
		$http.get(tableURL+'/filters')
			.then(function(response) {
				csrf = response.data.csrf;
				csrfHash = response.data.csrf_hash;
				next(response.data);
			});
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

	tables.downloadUrl = function() {
		return tableURL+'/export';
	};

	tables.downloadRowsUrl = function (ids) {
		var link = tableURL+'/export';
		link += encodeObject({rows: angular.toJson(ids)});
		return link;
	};

	tables.rowsAction = function (perform, data, fsuccess, ferror) {
		var link = '/rows';
		link += encodeObject({action: perform});
		reqpost(link, null, data, fsuccess, ferror);
	};

	

	tables.types = 
	{
		TEXT: 'Text',
		TEXTAREA: 'Long Text',
		CHECKBOX: 'Checkbox',
		FLOAT: 'Float',
		NUMBER: 'Integer',
		DATE: 'Date',
		EMAIL: 'Email',
		URL: 'URL'
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