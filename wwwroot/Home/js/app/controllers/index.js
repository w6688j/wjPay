/**
 * Created by Administrator on 2017/2/27.
 */
define(['izhuyan', 'app/filter'], function (izhuyan) {
    izhuyan.controller('index', ['$scope', '$http', 'config', function ($scope, $http, $c) {
        console.log($c);
    }]);
});