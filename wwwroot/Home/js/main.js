requirejs.config({
    //By default load any module IDs from js/lib
    baseUrl: '/js/',
    urlArgs: function (id, url) {
        var args = 'v=' + CONFIG.VERSION;
        if (url.indexOf('cdn.bootcss.com') !== -1) {
            args = '';
        }

        return (url.indexOf('?') === -1 ? '?' : '&') + args;
    },
    //except, if the module ID starts with "app",
    //load it from the js/app directory. paths
    //config is relative to the baseUrl, and
    //never includes a ".js" extension since
    //the paths config could be for a directory.
    paths: {
        "jquery": [
            '//cdn.bootcss.com/jquery/1.11.3/jquery.min',
            '//static.dev.izhuyan.com/style/js/jquery',
            'libs/jquery'
        ],
        "angular": [
            '//cdn.bootcss.com/angular.js/1.4.6/angular.min',
            '//static.dev.izhuyan.com/style/js/angular',
            'libs/angular'
        ],
        "angular-ui-router": [
            "//cdn.bootcss.com/angular-ui-router/0.2.18/angular-ui-router.min",
            "libs/angular-ui-router.min"
        ],
        'semaintic': [
            '//cdn.bootcss.com/semantic-ui/2.2.1/semantic.min',
            'libs/semantic.2.2.1.min'
        ],
        "ctrl": 'app/controllers',
        "srv": 'app/services',
        "dire": 'app/directives',
        "izhuyan": 'app/app',
        "config": 'app/config',
        "jquery-ui": 'libs/jquery-ui.min',
        "functions": 'app/functions',
        'angular-async-loader': 'libs/angular-async-loader.min'
    },
    shim: {
        "functions": ["jquery"],
        'angular': {
            exports: 'angular',
            deps: ['jquery']
        },
        'angular-ui-router': {
            deps: ["angular"]
        },
        'jquery-ui': ['jquery'],
        'semaintic': ['jquery'],
        'config': {
            deps: ['angular'],
            exports: 'config'
        }
    }
});

// Start the main app logic.
requirejs(['jquery', 'angular', 'izhuyan', 'semaintic', 'ctrl/loadingInterceptor', 'angular-ui-router', 'app/routers', 'app/filter', 'dire/main'],
    function ($, angular) {
        $(function () {
            angular.bootstrap(document, ['izhuyan'])
        });
    });