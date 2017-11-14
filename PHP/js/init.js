var app = angular.module('app', ['ngMaterial', 'ngMessages','chart.js','ngTableToCsv'])
	.config(function($mdThemingProvider) {
	  $mdThemingProvider.theme('altTheme')
	    .primaryPalette('grey').dark(); // specify primary color, all
	                            // other color intentions will be inherited
	                            // from default

	    $mdThemingProvider.theme('default')
	    .primaryPalette('green'); // specify primary color, all
	                            // other color intentions will be inherited
	                            // from default
	});

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