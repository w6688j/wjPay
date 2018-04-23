<?php
namespace Clhapp;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/16
 * Time: 22:10
 */
class View
{
    private $tVar = [];
    private $pattern = '/({)([^\d\s{}].+?)(})/i';

    public function __construct()
    {
    }

    public function assign($name, $value = '')
    {
        if (is_array($name)) {
            $this->tVar = array_merge($this->tVar, $name);
        } else {
            $this->tVar[$name] = $value;
        }
    }

    public function fetch($tpl)
    {
        $this->display($tpl, false);
    }

    public function display($tpl, $print = true)
    {
        $cacheFile = $this->build($tpl);

        if (false === $cacheFile) {
            return false;
        }
        ob_start();
        ob_implicit_flush(0);
        if ($this->tVar) {
            extract($this->tVar, EXTR_OVERWRITE);
        }

        include $cacheFile;

        // 获取并清空缓存
        $content = ob_get_clean();
        $replace = C('MODULE_REPLACE');
        if (is_array($replace)) {
            foreach ($replace as $k => $v) {
                $content = str_replace($k, $v, $content);
            }
        }

        if ($print) {
            if (!headers_sent()) {
                header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
                header("Cache-Control: no-cache, must-revalidate");
                header("Pragma: no-cache");
            }
            echo $content;
        } else {

            return $content;
        }
    }

    /**
     * build @desc 编译模板 并返回编译后的缓存路径
     *
     * @author wangjian
     *
     * @param $tpl
     *
     * @return string
     * @throws AppException
     */
    protected function build($tpl)
    {

        $cacheFile = C('CACHE_DIR') . 'cache/' . md5($tpl . C('VERSION')) . '.php';

        if (!APP_DEBUG && is_file($cacheFile)) {
            //非调试模式 并且存在缓存 则输出缓存
            return $cacheFile;
        }
        Log::record($cacheFile, 'create tpl');
        $tplContent = file_get_contents($tpl);

        if (false === $tplContent) {
            throw new AppException('读取模板内容失败~filepath:' . $tpl);
        }
        $buildContent = $this->parse($tplContent);
        if (!is_dir(dirname($cacheFile))) {
            if (!mkdir(dirname($cacheFile), 0755, true)) {
                throw new AppException('创建文件夹：' . dirname($cacheFile) . '失败');
            }
        }
        if (!file_put_contents($cacheFile, $buildContent)) {
            throw new AppException('写入模板缓存失败~');
        }

        return $cacheFile;
    }

    /**
     * parse @desc 解析模板
     *
     * @author wangjian
     *
     * @param $content
     *
     * @return mixed
     */
    protected function parse($content)
    {
        return preg_replace_callback($this->pattern, [$this, 'parseStr'], $content);
    }

    /**
     * parseStr @desc 解释函数和变量
     *
     * @author wangjian
     *
     * @param $tagStr
     *
     * @return string
     */
    protected function parseStr($tagStr)
    {
        if (is_array($tagStr)) {
            $tagStr = $tagStr[2];
        }
        $tagStr = stripslashes($tagStr);
        //还原非模板标签
        if (preg_match('/^[\s|\d]/is', $tagStr)) //过滤空格和数字打头的标签
        {
            return '{' . $tagStr . '}';
        }
        $flag = substr($tagStr, 0, 1);
        $name = substr($tagStr, 1);
        if ('$' == $flag) { //解析模板变量 格式 {$varName}
            return $this->parseVar($name);
        } elseif (':' == $flag) { // 输出某个函数的结果
            return '<?php echo ' . $name . ';?>';
        } elseif ('~' == $flag) { // 执行某个函数
            return '<?php ' . $name . ';?>';
        }

        // 未识别的标签直接返回
        return '{' . $tagStr . '}';
    }

    /**
     * parseVar @desc 解析变量
     *
     * @author wangjian
     *
     * @param $var
     *
     * @return string
     */
    protected function parseVar($var)
    {
        if (false === strpos($var, '.')) {
            return '<?php echo $' . $var . '; ?>';
        } else {
            list($array, $key) = explode('.', $var);

            return '<?php echo $' . $array . "['" . $key . "']; ?>";
        }
    }
}