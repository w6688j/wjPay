<?php
namespace Clhapp;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/16
 * Time: 22:10
 */
interface iCache
{
    /**
     * set @desc 消息
     *
     * @author wangjian
     *
     * @param string $key   key
     * @param mixed  $value value
     * @param int    $time  缓存时间
     *
     * @return mixed
     */
    public function set($key, $value, $time);

    /**
     * get @desc 读取某个key的缓存
     *
     * @author wangjian
     *
     * @param string $key key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * del @desc 删除某个key的缓存
     *
     * @author wangjian
     *
     * @param string $key key
     *
     * @return mixed
     */
    public function del($key);
}

class Mcache implements iCache
{
    /**
     * @var \Memcached
     */
    private $memcache;
    private $conf = '';
    private $key_pre = '';
    private $max_expire = 0;

    /**
     * Mcache constructor. 构造函数
     *
     * @param string $config
     */
    public function __construct($config = '')
    {
        if (!$config) {
            $this->conf = 'DEFAULT';
        } else {
            $this->conf = $config;
        }
        $conf = C('MEMCACHE_' . $this->conf);
        if (empty($conf) || !(isset($conf['ip']) && isset($conf['port']))) {
            Log::write('memcahce 配置错误:' . $this->conf, Log::ERR);

            return;
        }
        if (class_exists('Memcached')) {
            $this->memcache = new \Memcached();
            $this->memcache->addServer($conf['ip'], $conf['port']);
            $stat = $this->memcache->getStats();
            if ($stat[$conf['ip'] . ':' . $conf['port']]['pid'] <= 0) {
                Log::write('memcache 配置错误，请检查' . var_export($conf, true), Log::ERR);

                return;
            }
        } else {
            $this->memcache = new \Memcache();
            $this->memcache->addServer($conf['ip'], $conf['port']);
        }
        if (isset($conf['key_pre'])) {
            $this->key_pre = $conf['key_pre'];
        }
        if (isset($conf['max_expire'])) {
            $this->max_expire = $conf['max_expire'];
        }
    }

    /**
     * set @desc 设置缓存
     *
     * @author wangjian
     *
     * @param string $key   缓存key
     * @param mixed  $value 缓存的值
     * @param int    $time  缓存的时间
     *
     * @return bool
     */
    public function set($key, $value, $time = 0)
    {
        if ($this->key_pre) {
            $key = $this->key_pre . $key;
        }
        if ($this->max_expire && ($time == 0 || $time > $this->max_expire)) {
            $time = $this->max_expire;
        }
        Log::record('set :' . $key . ' time:' . $time, Log::MEMCACHE);
        if (class_exists('Memcached')) {
            return $this->memcache->set($key, $value, $time);
        } else {
            return $this->memcache->set($key, $value, null, $time);
        }

    }

    /**
     * get @desc 缓存的时间
     *
     * @author wangjian
     *
     * @param string $key 缓存key
     *
     * @return array|mixed|string
     */
    public function get($key)
    {
        if ($this->key_pre) {
            $key = $this->key_pre . $key;
        }
        Log::record('get :' . $key, Log::MEMCACHE);

        return $this->memcache->get($key);
    }

    /**
     * del @desc 删除索引为key的缓存
     *
     * @author wangjian
     *
     * @param string $key key
     *
     * @return bool
     */
    public function del($key)
    {
        if ($this->key_pre) {
            $key = $this->key_pre . $key;
        }
        Log::record('del :' . $key, Log::MEMCACHE);

        return $this->memcache->delete($key);
    }

    /**
     * increment @desc 增长队列
     *
     * @author wangjian
     *
     * @param string $key key
     * @param int    $inc 增长值
     * @param int    $e   时间
     *
     * @return bool|int
     */
    public function increment($key, $inc = 1, $e = 86400)
    {
        if ($this->key_pre) {
            $new_key = $this->key_pre . $key;
        } else {
            $new_key = $key;
        }
        Log::record('increment :' . $key, Log::MEMCACHE);
        $rs = $this->memcache->increment($new_key, $inc);
        if ($rs == false) {
            if ($this->set($key, $inc, $e)) {
                return $inc;
            } else {
                return false;
            }
        }

        return $rs;
    }

    /**
     * decrement @desc 自减队列
     *
     * @author wangjian
     *
     * @param string $key 索引
     * @param int    $inc 增长值
     *
     * @return int
     */
    public function decrement($key, $inc = 1)
    {
        if ($this->key_pre) {
            $key = $this->key_pre . $key;
        }
        Log::record('decrement :' . $key, Log::MEMCACHE);

        return $this->memcache->decrement($key, $inc);
    }
}

/**
 * desc:抽象工厂接口
 * Interface iCacheFactory
 */
interface iCacheFactory
{
    static public function provide($config = '');
}

/**
 * desc: Mcache 工厂类
 * Class McacheFactory
 */
class McacheFactory implements iCacheFactory
{
    static private $Mcache;

    /**
     * provide @desc 返回Mcache实例
     *
     * @author wangjian
     *
     * @param string $config
     *
     * @return Mcache
     */
    static public function provide($config = '')
    {
        if (!isset(self::$Mcache[$config])) {
            self::$Mcache[$config] = new Mcache($config);
        }

        return self::$Mcache[$config];
    }
}

