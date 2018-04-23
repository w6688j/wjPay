define(['izhuyan', 'config'], function (izhuyan, config) {
    izhuyan.run(['$state', '$stateParams', '$rootScope', function ($state, $stateParams, $rootScope) {
        $rootScope.$state = $state;
        $rootScope.$stateParams = $stateParams;
        $rootScope.loading_window = false;
    }]);
    izhuyan.config(['$stateProvider', '$urlRouterProvider', '$locationProvider', function ($stateProvider, $urlRouterProvider, $locationProvider) {
        $urlRouterProvider.otherwise('/');
        $stateProvider
            .state('index', {
                url: '/',
                templateUrl: config.tpl('index/index'),
                controllerUrl: 'ctrl/index',
                controller: 'index'
            })
        ;
        $locationProvider.html5Mode(true).hashPrefix('!');
    }]);

});