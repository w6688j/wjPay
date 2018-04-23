define(['angular', 'angular-async-loader', 'module', 'exports', 'functions'], function (angular, asyncLoader, module, exports) {

    var app = angular.module('izhuyan', ['ui.router', 'ajaxLoading']);
    asyncLoader.configure(app);

    module.exports = app;

    return app;
});