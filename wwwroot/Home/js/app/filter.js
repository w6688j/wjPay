define(['izhuyan'], function (izhuyan) {
    izhuyan.filter('default', function () {
        return function (input, default_text) {
            if (!input || input == '￥0.00') {
                return default_text;
            }
            return input;
        };
    })
        .filter('priceNum', function () {
            return function (input) {
                if (!input) {
                    return '0';
                }
                return input.toString().split('.')[0];
            }
        })
        .filter('priceDecimal', function () {
            return function (input) {
                if (!input || input.toString().indexOf('.') <= 0) {
                    return '00';
                }
                return input.toString().split('.')[1];
            }
        })
});