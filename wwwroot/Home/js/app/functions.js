//提示消息框
function tip(options) {
    var conf = {
        html: "<div class=\"mingming-tips\" id=\"__ID__\"><div class=\"box\"><div class=\"shade\"><\/div><p>__TEXT__<\/p><\/div><\/div>",
        text: '提示',
        url: '',
        reload: false,
        time: 1000,
        id: new Date().valueOf()
    };
    if (typeof options == "string") {
        conf.text = options;
    } else {
        conf = $.extend(conf, options);
    }

    $('body').append(conf.html.replace('__TEXT__', conf.text).replace('__ID__', conf.id));
    $('#' + conf.id).fadeIn(500);
    if (conf.url) {
        $.URL.url(conf.url);
        conf.reload = true;
    }
    setTimeout(function () {
        if (conf.reload) {
            $.URL.reload();
        } else {
            $('#' + conf.id).fadeOut(1000, null, function () {
                $(this).remove();
            });
        }
    }, conf.time);
}

//对浮点数格式化，防止出现0.99999998的现象(f为浮点数，size中保留小数位数)
function formatfloat(f, size) {
    var tf = f * Math.pow(10, size);
    tf = Math.round(tf + 0.000000001);
    tf = tf / Math.pow(10, size);
    return tf;
}

$.extend({
    loading: {
        imgsrc: "<div class=\"spinner\"><div class=\"rect1\"><\/div><div class=\"rect2\"><\/div><div class=\"rect3\"><\/div><div class=\"rect4\"><\/div><div class=\"rect5\"><\/div><\/div>",
        html: "<div id=\"jquery-loading\"><div class=\"shade\"><\/div><div class=\"loading-img\">__IMG_SRC__<\/div><p class=\"loading-text\"><\/p><\/div>",
        selector: '#jquery-loading',
        init: function () {
            this.hide();
            $('body').append(this.html.replace('__IMG_SRC__', this.imgsrc));
        },
        hide: function () {
            $(this.selector).length > 0 && $(this.selector).remove();
        },
        shade: function (timeout) {
            this.init();
            timeout = parseInt(timeout) || 0;
            if (timeout > 0) {
                setTimeout(function () {
                    $.loading.hide();
                }, timeout);
            }
        },
        text: function (loading_text, timeout) {
            this.shade(timeout);
            $(this.selector).children('.loading-img').show();
            $(this.selector).children('.loading-text').show().html(loading_text);
        }
    },
    tip: tip,
    formatfloat: formatfloat
});



