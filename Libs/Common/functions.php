<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/16
 * Time: 22:39
 */

/**
 * C @desc 配置设置
 *
 * @author wangjian
 *
 * @param null $name    索引key
 * @param null $value   值
 * @param null $default 默认
 *
 * @return null
 */
function C($name = null, $value = null, $default = null)
{
    static $_config = [];
    // 无参数时获取所有
    if (empty($name)) {
        return $_config;
    }
    // 优先执行设置获取或赋值
    if (is_string($name)) {
        if (!strpos($name, '.')) {
            $name = strtoupper($name);

            if (is_null($value)) {
                return isset($_config[$name]) ? $_config[$name] : $default;
            }
            $_config[$name] = $value;

            return null;
        }
        // 二维数组设置和获取支持
        $name    = explode('.', $name);
        $name[0] = strtoupper($name[0]);
        if (is_null($value)) {
            return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : $default;
        }
        $_config[$name[0]][$name[1]] = $value;

        return null;
    }
    // 批量设置
    if (is_array($name)) {
        $_config = array_merge($_config, array_change_key_case($name, CASE_UPPER));

        return null;
    }

    return null; // 避免非法参数
}

/**
 * array2str @desc 将数组里某个字段的值连接成字符串
 *
 * @author wangjian
 *
 * @param array  $array 目标数组
 * @param string $key   目标key
 * @param string $sep   链接字符
 *
 * @return string
 */
function array2str($array, $key = 'id', $sep = ',')
{
    $data = [];
    if (is_array($array)) {
        foreach ($array as $row) {
            if (isset($row[$key])) {
                $data[] = $row[$key];
            }
        }
    }
    if ($data) {
        return implode($sep, $data);
    }

    return '';
}

/**
 * checkUTF8 @desc 检验UTF8
 *
 * @author wangjian
 *
 * @param string $string 检测字符串
 *
 * @return bool
 */
function checkUTF8($string)
{
    return $string === iconv('GBK', 'UTF-8', iconv('UTF-8', 'GBK', $string));
}

/**
 * F @desc 快速文件数据读取和保存 针对简单类型数据 字符串、数组
 *
 * @author wangjian
 *
 * @param string $name  缓存名称
 * @param string $value 缓存值
 * @param string $path  缓存路径
 *
 * @return array|bool|int|mixed|string
 */
function F($name, $value = '', $path = '')
{
    if (empty($path)) {
        $path = C('CACHE_DIR');
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
    static $_cache = [];
    $filename = $path . $name . '.php';
    if ('' !== $value) {
        if (is_null($value)) {
            // 删除缓存
            return false !== strpos($name, '*') ? array_map("unlink", glob($filename)) : unlink($filename);
        } else {
            // 缓存数据
            $dir = dirname($filename);
            // 目录不存在则创建
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $_cache[$name] = $value;

            return file_put_contents($filename, "<?php\treturn " . var_export($value, true) . ";?>");
        }
    }
    if (isset($_cache[$name])) {
        return $_cache[$name];
    }
    // 获取缓存数据
    if (is_file($filename)) {
        $value         = include $filename;
        $_cache[$name] = $value;
    } else {
        $value = false;
    }

    return $value;
}

/**
 * api_return @desc 接口返回
 *
 * @author wangjian
 *
 * @param array  $data       数据
 * @param bool   $background 是否返回后执行
 * @param string $callback   ?
 */
function api_return($data, $background = false, $callback = '')
{
    if ($callback) {
        $data_new = $callback . '(' . json_encode($data, JSON_UNESCAPED_UNICODE) . ')';
    } else {
        $data_new = json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    if (!headers_sent()) {
        header("Content-type:application/json;charset=utf-8");
        header('Content-Length: ' . strlen($data_new));
    }
    ob_end_clean();
    echo $data_new;
    if ($background && function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    } else {
        die();
    }
}

function __autoload($className)
{
    $classes  = explode('\\', $className);
    $argCount = count($classes);
    $files    = [];

    $file = CLHAPP_PATH . '/Vendor/' . str_replace('\\', '/', strtr($className, C('CLASS_MAP'))) . '.php';
    if (is_readable($file)) {
        require_once $file;

        return;
    }

    if ($argCount == 2) {
        $files[] = CLHAPP_PATH . '/Classes/' . $classes[1] . EXT;
    } elseif ($argCount > 2) {
        if ($classes[2] == 'Model') {
            $files[] = dirname(APP_PATH) . '/' . $classes[1] . '/' . $classes[2] . '/' . $classes[3] . EXT;
        }
        if (in_array($classes[1], explode(',', C('APP_LIST')))) {
            $tmp = APP_PATH;
            foreach ($classes as $k => $f) {
                if ($k <= 1) {
                    continue;
                }
                $tmp .= "/" . $f;
            }

            $tmp .= EXT;
            $files[] = $tmp;
        }
        $files[] = CLHAPP_PATH . '/Classes/' . str_replace('\\', '/', substr($className, 7)) . EXT;

    } else {
        return;
    }
    foreach ($files as $file) {
        if (is_readable($file)) {
            require_once $file;
        }
    }
}

/**
 * getDebugTrace @desc 获取堆栈信息和各函数作者
 *
 * @author wangjian
 *
 * @param array $users 用户数组
 *
 * @return array
 */
function getDebugTrace(&$users = [])
{
    ob_start();
    debug_print_backtrace();
    $trace = ob_get_clean();
    ob_end_flush();
    $trace = explode("\n", $trace);
    $data  = [];
    $users = [];
    array_shift($trace);

    try {
        foreach ($trace as $v) {
            $tmp = 'system';
            preg_match('/(#[0-9]+)  ((.*?)(->|::))?(.*)\(\) called at \[([^\]]+)\]/', $v, $match);
            if (!$match) {
                continue;
            }
            list(, $index, $callname, $class, , $method, $file) = $match;
            if ($class) {
                $classObj = new \ReflectionClass($class);
                $doc      = $classObj->getDocComment() . $classObj->getMethod($method)->getDocComment();
            } else {
                $classObj = new \ReflectionFunction($method);
                $doc      = $classObj->getDocComment();
            }
            if ($doc) {
                preg_match_all('/@author ([\w\d]+)/', $doc, $match);
                $match[1] && $tmp = $users[] = implode(',', $match[1]);
            }

            $data[] = [$v, $index, $callname . $method . '()', $class, $method, $file, $tmp];
        }
    } catch (\Exception $e) {
        $data[] = $e->getCode() . ':' . $e->getMessage();
    }

    return $data;
}

/**
 * @desc   getNextMonthDayTimestamp 获取某一天的下一个月的这一天
 *         eg 2016-11-01 -> 2016-12-01
 *         eg 2016-03-31 -> 2016-04-30
 * @author wangjian
 *
 * @param int $timestamp 某天的时间戳
 *
 * @return int
 */
function getNextMonthDayTimestamp($timestamp)
{
    $nextMonthTimestamp = strtotime('+1 month', $timestamp);
    $todayMonth         = date('m', $timestamp);
    if (in_array($todayMonth, [2, 4, 6, 7, 9, 11, 12])) {
        return $nextMonthTimestamp;
    }
    $nextMonth = date('m', $nextMonthTimestamp);

    if ($nextMonth == $todayMonth + 1) {
        return $nextMonthTimestamp;
    }

    //返回下一个月的月末
    $todayLastDay = date('Y-m-t', $timestamp);

    return strtotime(date('Y-m-t', strtotime($todayLastDay) + 86400));
}

/**
 * @desc   getPreMonthDayTimestamp
 * @author wangjian
 *
 * @param $timestamp
 *
 * @return int
 */
function getPreMonthDayTimestamp($timestamp)
{
    $preMonthTimestamp = strtotime('-1 month', $timestamp);
    $todayMonth        = date('m', $timestamp);

    $preMonth = date('m', $preMonthTimestamp);

    if ($preMonth == $todayMonth - 1) {
        return $preMonthTimestamp;
    }

    //返回下一个月的月末
    $todayFirstDay = date('Y-m-01', $timestamp);

    return strtotime(date('Y-m-t', strtotime($todayFirstDay) - 86400));
}

/**
 * underline2Camel @desc 下划线转驼峰法
 *
 * @author wangjian
 *
 * @param string $str 下划线命名方式字符串
 *
 * @return string 驼峰法命名字符串
 */
function underline2Camel($str)
{
    return str_replace('_', '', ucwords($str, '|'));

}

/**
 * getLoginUrl @desc 获取登录验证地址
 *
 * @author wangjian
 *
 * @param string $redirect 跳转地址
 * @param array  $params   附加参数
 *
 * @return string
 */
function getLoginUrl($redirect = '', $params = [])
{
    if ($redirect == '') {
        if (defined('__URL__')) {
            $redirect = __URL__;
        }
    }
    $redirect && $params['redirect'] = $redirect;
    if (strpos($_SERVER['HTTP_HOST'], 'chenmm.cn')) {
        return 'https://login.chenmm.cn/login.html?' . http_build_query($params);
    } elseif (strpos($_SERVER['HTTP_HOST'], 'test.axhome.com.cn')) {
        return 'https://account.test.axhome.com.cn/login.html?' . http_build_query($params);
    } else {
        return 'https://account.axhome.com.cn/login.html?' . http_build_query($params);
    }
}

/**
 * curl @desc 请求地址
 *
 * @author wangjian
 *
 * @param string $url       地址
 * @param null   $post_data 数据
 * @param array  $config    配置
 * @param array  $chinfo    信息
 *
 * @return mixed
 */
function curl($url, $post_data = null, $config = [], &$chinfo = [])
{
    \Clhapp\Log::write($url, \Clhapp\Log::WARNING);
    settype($config, 'array');
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_ENCODING, true);
    if ($post_data) {
        //一定需要编码,否则接收方会错误
        if (isset($config['upload-file']) && $config['upload-file']) {
            unset($config['upload-file']);
            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        }

    }
    $CURLOPT_HTTPHEADER = [
        'Accept-Encoding:gzip,deflate,sdch',
        'User-Agent:Mozilla/5.0 (Windows NT 5.1; rv:2.0) Gecko/20100101 Firefox/4.0',
        "Referer:{$url}",
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $CURLOPT_HTTPHEADER);
    foreach ($config as $k => $v) {
        if (is_numeric($k)) {
            @curl_setopt($ch, $k, $v);
        }

    }

    $curl_data            = curl_exec($ch);
    $curl_error           = curl_error($ch);
    $chinfo               = curl_getinfo($ch);
    $chinfo['curl_error'] = $curl_error;
    curl_close($ch);
    //如果是跳转
    if ($config['not301'] != 1 && in_array($chinfo['http_code'], ['301', '302']) && $chinfo['redirect_url']) {
        //防止死循环重定向
        $config['not301'] = 1;
        $curl_data        = curl($chinfo['redirect_url'], $post_data, $config);
    }

    return $curl_data;
}

/**
 * getIpAddr @desc 获取ip所在地
 *
 * @author wangjian
 *
 * @param string $ip ip
 *
 * @return string
 */
function getIpAddr($ip)
{
    $ipSign = ip2long($ip);
    if ($ipSign === false) {
        //该ip不合法
        return $ip;
    }
    $ipAddr = \Clhapp\McacheFactory::provide()->get('ipaddress_' . $ipSign);
    if ($ipAddr) {
        return $ipAddr;
    }
    $url     = "http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip;
    $content = curl($url, null, [CURLOPT_TIMEOUT => 3]);
    $data    = json_decode($content, true);

    if (!$data || $data['code'] != 0) {
        return '未知地址' . $ip;
    } else {
        $data   = $data['data'];
        $ipAddr = implode('-', array_filter([$data['country'], $data['area'], $data['region'], $data['city'], $data['isp']]));
    }
    \Clhapp\McacheFactory::provide()->set('ipaddress_' . $ipSign, $ipAddr);

    return $ipAddr;
}

/**
 * trace @desc 调试
 *
 * @author wangjian
 *
 * @param string $value  要调试的变量
 * @param string $label  标签
 * @param string $level  等级
 * @param bool   $record 是否记录
 *
 * @return array
 */
function trace($value = '[trace]', $label = '', $level = \Clhapp\Log::DEBUG, $record = false)
{
    static $_trace = [];
    if (!APP_DEBUG) {
        return [];
    }
    if ('[trace]' === $value) {
        // 获取trace信息
        return $_trace;
    } else {
        if ($record) {
            \Clhapp\Log::record($value, $label);
        } else {
            if (!isset($_trace[$level])) {
                $_trace[$level] = [];
            }

            if (is_object($value)) {
                $value = get_object_vars($value);
            }
            if (is_bool($value)) {
                $value = (bool)$value ? 'true' : 'false';
            } else {
                if (IS_CLI && checkUTF8(serialize($value))) {
                    $value = utf8togbk($value);
                }
            }

            if ($label) {
                if (IS_CLI && checkUTF8($label)) {
                    $label = utf8togbk($label);
                }

                $value = $label . '=>' . print_r($value, true);
            } else {
                $value = print_r($value, true);
            }
            if (IS_CLI) {
                echo $value . PHP_EOL;
            } else {
                $_trace[$level][] = $value;
            }

        }
    }
}

/**
 * utf8togbk @desc utf8转换成gbk
 *
 * @author wangjian
 *
 * @param mixed $data 转换的数据
 *
 * @return array|string
 */
function utf8togbk($data)
{
    $result = [];
    if (is_object($data)) {
        utf8togbk(get_object_vars($data));
    } elseif (is_array($data)) {
        foreach ($data as $k => $v) {
            //注意转换了key的内容
            $k = iconv('UTF-8', 'GB18030', $k);
            if (is_array($v)) {
                $result[$k] = utf8togbk($v);
            } else {
                if (!is_numeric($v)) {
                    $result[$k] = iconv('UTF-8', 'GB18030', $v);
                } else {
                    $result[$k] = $v;
                }
            }
        }

        return $result;
    } else {
        if (!is_numeric($data)) {
            return iconv('UTF-8', 'GB18030', $data);
        } else {
            return $data;
        }
    }
}

/**
 * gbktoutf8 @desc gbk转换成utf8
 *
 * @author wangjian
 *
 * @param mixed $data 转换的数据
 *
 * @return array|string
 */
function gbktoutf8($data)
{
    $result = [];
    if (is_object($data)) {
        gbktoutf8(get_object_vars($data));
    } elseif (is_array($data)) {
        foreach ($data as $k => $v) {
            //注意转换了key的内容
            $k = iconv('GB18030', 'UTF-8', $k);
            if (is_array($v)) {
                $result[$k] = gbktoutf8($v);
            } else {
                if (!is_numeric($v)) {
                    $result[$k] = iconv('GB18030', 'UTF-8', $v);
                } else {
                    $result[$k] = $v;
                }
            }
        }

        return $result;
    } else {
        if (!is_numeric($data)) {
            return iconv('GB18030', 'UTF-8', $data);
        } else {
            return $data;
        }
    }
}

/**
 * redirect @desc URL重定向
 *
 * @author wangjian
 *
 * @param string  $url  重定向的URL地址
 * @param integer $time 重定向的等待时间（秒）
 * @param string  $msg  重定向前的提示信息
 */
function redirect($url, $time = 0, $msg = '')
{
    //多行URL地址支持
    $url = str_replace(["\n", "\r"], '', $url);
    if (empty($msg)) {
        $msg = "系统将在{$time}秒之后自动跳转到{$url}！";
    }
    if (!headers_sent()) {
        // redirect
        if (0 === $time) {
            header('Location: ' . $url);
            exit();
        } else {
            header("refresh:{$time};url={$url}");
            echo($msg);
        }
    } else {
        $str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if ($time != 0) {
            $str .= $msg;
        }
        exit($str);
    }
}
