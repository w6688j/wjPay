define(['izhuyan', 'config'], function (izhuyan, $c) {
    return izhuyan
        .directive('navigation', [function () {
            return {
                restrict: 'E',
                replace: true,
                templateUrl: $c.tpl('public/navbar'),
                link: function () {
                    console.log('navbar');
                }
            };
        }])
        .directive('footer', [function () {
            return {
                restrict: 'E',
                replace: true,
                templateUrl: $c.tpl('public/footer'),
                link: function () {
                    console.log('footer');
                }
            };
        }])
});