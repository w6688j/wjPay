var config = {
    tpl: function (tpl) {
        return '//' + window.location.host + "/js/app/views/" + tpl + '.tpl.html?v=' + CONFIG.VERSION;
    },
    parse: function (query) {
        return '//' + window.location.host + "/ajax/" + query + '.html';
    }
};
