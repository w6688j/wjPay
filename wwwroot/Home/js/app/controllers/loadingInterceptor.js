/**
 * 拦截器 全局$http注入loading效果
 */
define(['jquery', 'angular', 'config'], function ($, angular, config) {
    angular.module('ajaxLoading', [])
        .config(function ($httpProvider, $provide) {
            // Use x-www-form-urlencoded Content-Type
            $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
            $httpProvider.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
            /**
             * The workhorse; converts an object to x-www-form-urlencoded serialization.
             * @param {Object} obj
             * @return {String}
             */
            var param = function (obj) {
                var query = '', name, value, fullSubName, subName, subValue, innerObj, i;

                for (name in obj) {
                    value = obj[name];
                    if (value instanceof Array) {
                        for (i = 0; i < value.length; ++i) {
                            subValue = value[i];
                            fullSubName = name + '[' + i + ']';
                            innerObj = {};
                            innerObj[fullSubName] = subValue;
                            query += param(innerObj) + '&';
                        }
                    }
                    else if (value instanceof Object) {
                        for (subName in value) {
                            subValue = value[subName];
                            fullSubName = name + '[' + subName + ']';
                            innerObj = {};
                            innerObj[fullSubName] = subValue;
                            query += param(innerObj) + '&';
                        }
                    }
                    else if (value !== undefined && value !== null)
                        query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
                }

                return query.length ? query.substr(0, query.length - 1) : query;
            };
            // Override $http service's default transformRequest
            $httpProvider.defaults.transformRequest = [function (data) {
                return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
            }];
            $httpProvider.interceptors.push('loadingInterceptor');
            $provide.factory('config', ['$http', '$q', function ($h, $q) {
                var options = {};
                config.getOptions = function () {
                    if (options.cost) {
                        return $q.when(options);
                    }
                    return $h.get(config.parse('common/config'), {cache: true})
                        .then(function (data) {
                            if (data.data.code == 0) {
                                options = data.data.data;
                                return options;
                            } else {
                                tip('加载主要配置失败~请刷新后重试~');
                            }
                        });
                };

                return config;
            }])
        })

        .directive('loading', ['$rootScope', function ($rootScope) {
            return {
                replace: true,
                restrict: 'AE',
                template: '<div class="loading-style"><div class="ui tiny tyred progress"><div class="bar"></div></div></div>',
                link: function (scope, elem) {
                    $rootScope.is_loading = 0;
                    var percent = 0, int, obj = $('.progress', elem);

                    $rootScope.$watch('is_loading', function () {
                        clearInterval(int);
                        if ($rootScope.is_loading != 0) {
                            if (percent == 100) {
                                percent = parseInt(Math.random() * 100) % 50;
                            }
                            obj.progress('reset');
                            obj.progress({
                                percent: percent
                            });
                            obj.show();
                            int = setInterval(function () {
                                percent = percent + parseInt(Math.random() * 100) % 20;
                                if (percent > 90) {
                                    percent = 90
                                }
                                obj.progress({percent: percent})
                            }, 500);
                        } else {
                            percent = 100;
                            obj.progress(
                                {
                                    percent: percent,
                                    onSuccess: function () {
                                        obj.hide();
                                    }
                                })
                        }
                    })
                }
            };
        }])
        .directive('loadingWindow', ['loadMsg', function ($loadMsg) {
            return {
                replace: true,
                restrict: 'AE',
                template: '<div class="ui modal small"><div class="header">请稍后...</div><div class="content"><div class="ui icon message"><i class="notched circle loading icon"></i><div class="content"><p>{{msg}}</p></div></div></div></div>',
                link: function (scope, elem) {
                    $loadMsg.init(scope, elem);
                }
            };
        }])
        .factory('loadMsg', function () {
            var obj = function () {
                this.is_init = false;
            };
            obj.prototype = {
                init: function ($scope, elem) {
                    this.elem = elem;
                    this.scope = $scope;
                    this.is_init = true;
                },
                show: function (msg) {
                    if (!this.is_init) {
                        return;
                    }
                    var self = this;
                    this.scope.msg = msg;

                    this.scope.$apply(function () {
                        $(self.elem)
                            .modal('setting', 'closable', false)
                            .modal('show');
                    })
                },
                hide: function () {
                    if (!this.is_init) {
                        return;
                    }
                    $(this.elem)
                        .modal('hide');
                }
            };
            return new obj();
        })
        .factory('loadingInterceptor', function ($q, $rootScope) {
            $rootScope.reload = function () {
                $.get(config.parse('common/reload'), function (json) {
                    if (json.code == 0) {
                        $rootScope.$emit('reload-data');
                    }
                });
            };
            return {
                request: function (config) {
                    $rootScope.is_loading++;
                    config.method == 'GET' && (config.headers['X-Requested-With'] = 'XMLHttpRequest');
                    return config || $q.when(config);
                },
                response: function (response) {
                    $rootScope.is_loading--;
                    return response || $q.when(response);
                },
                responseError: function (rejection) {
                    $rootScope.is_loading--;
                    return $q.reject(rejection);
                }
            };
        });
});