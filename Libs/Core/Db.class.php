<?php

namespace Clhapp;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/16
 * Time: 22:10
 */
class DB
{
    const CURRENT_DB = '_current';
    const SELECT_DB = 'SELECT';
    const DELETE_DB = 'DELETE';
    const INSERT_DB = 'INSERT';
    const REPLACE_DB = 'REPLACE';
    const UPDATE_DB = 'UPDATE';
    const STRING_WHERE = '_string';
    const LOGIC_WHERE = '_logix'; //and 和 or 运算的操作
    const EXP_WHERE = 'exp'; //索引
    const VALUE_WHERE = 'value'; //绑定变量
    const MEMCACHE_KEY_PRE = 'sql_'; //memcache索引前缀

    static private $db = null; //数据库资源
    static public $sql_count = 0;
    private $tableName = null; //表名
    private $where = ''; //where条件
    private $fields = '*';
    private $order = '';
    private $limit = '';
    private $sql = '';
    private $expire = 0;
    private $expire_pre = '';

    private $pk = ''; //主键
    private $db_fields = []; //该表中所有字段
    private $db_fields_info = []; //该表中所有字段信息数组 key为字符名称
    private $bind = [];//绑定变量
    private $debug_bind = [];//调试绑定

    private $data = [];

    static private $last_id = null;
    static private $row_count = 0;

    static private $error = '';
    static private $_exp = ['>', '<', '>=', '<=', 'like', 'notlike', '=', '!=', 'in', 'exp', 'not in'];

    /**
     * M @desc M 初始化DB
     *
     * @author wangjian
     *
     * @param null $dbini 数据库配置
     *
     * @return DB
     * @throws AppException
     */
    static function M($dbini = null)
    {
        $db = new DB();
        if (empty($dbini)) {
            $dbini  = 'default';
            $dbconf = C('DB');
        } else {
            $dbconf = C('DB_' . $dbini);
        }

        if (!isset(self::$db[$dbini])) {
            if (empty($dbconf)) {
                throw new AppException('数据库配置不存在');
            }
            $driver = '\\Clhapp\\Db\\' . $dbconf['type'];

            self::$db[$dbini] = new $driver($dbconf);
        }
        self::$db[self::CURRENT_DB] = self::$db[$dbini];

        return $db;
    }

    /**
     * table @desc 设置表名
     *
     * @author wangjian
     *
     * @param string $tableName 表名称
     *
     * @return $this
     */
    public function table($tableName)
    {
        $this->tableName = $tableName;

        return $this;
    }

    /**
     * where @desc 查询语句
     *
     * @author wangjian
     *
     * @param mixed $data 查询条件
     *
     * @return $this
     */
    public function where($data)
    {
        $this->parseWhere($data);

        return $this;
    }

    /**
     * limit @desc 分页
     *
     * @author wangjian
     *
     * @param int $start 开始数量
     * @param int $end   显示数量
     *
     * @return $this
     */
    public function limit($start, $end)
    {
        $this->limit = 'LIMIT ' . (int)$start . ',' . (int)$end;

        return $this;
    }

    /**
     * order @desc 排序
     *
     * @author wangjian
     *
     * @param string $order 排序
     *
     * @return $this
     */
    public function order($order)
    {
        if ($order) {
            $this->order = "ORDER BY " . $order . ' ';
        }

        return $this;
    }

    /**
     * select @desc select
     *
     * @author wangjian
     * @return array|bool
     */
    public function select()
    {
        $this->parseSql();

        return $this->_query();
    }

    /**
     * _query @desc _query
     *
     * @author wangjian
     *
     * @param bool $one 是否查找一条数据
     *
     * @return array|bool|mixed|string
     */
    private function _query($one = false)
    {
        if (!$this->sql) {
            return false;
        }
        if (($this->expire > 0 && $this->expire !== true) || $this->expire === -1) {
            //是否需要读取缓存
            $data = $this->readCache();
            if ($data) {

                //缓存存在则直接返回
                return $data;
            }

        }
        self::$sql_count++;
        $t1 = microtime(true);
        if ($one) {
            $data = $this->getCurrentDblink()->find($this->sql, $this->bind);
        } else {
            $data = $this->getCurrentDblink()->select($this->sql, $this->bind);
        }
        self::$error = $this->getCurrentDblink()->getError();
        $t2          = microtime(true);
        if (C('SHOW_SQL')) {
            Log::write('[' . self::$sql_count . ']' . strtr($this->sql, $this->debug_bind) . "[Exec:" . ($t2 - $t1) . "s]", Log::SQL);
            $t2 - $t1 >= 0.1 && Log::write(strtr($this->sql, $this->debug_bind) . "[Exec:" . ($t2 - $t1) . "s]", '/dev/shm/slow_sql.cutlog');
        }
        if (self::$error) {
            Log::write('[' . self::$sql_count . ']' . strtr($this->sql, $this->debug_bind) . "[Exec:" . ($t2 - $t1) . "s]", Log::ERR);
            Log::write(Log::COLOR_ERROR . self::$error . Log::COLOR_END, Log::ERR);

            return false;
        } else {
            $this->writeCache($data);

            return $data;
        }
    }

    /**
     * @return \Clhapp\Db\Pdo
     */
    private function getCurrentDblink()
    {
        return self::$db[self::CURRENT_DB];
    }

    /**
     * _execute @desc 执行sql语句
     *
     * @author wangjian
     * @return bool
     */
    private function _execute()
    {
        if (!$this->sql) {
            return false;
        }
        self::$sql_count++;
        $t1              = microtime(true);
        $result          = $this->getCurrentDblink()->exec($this->sql, $this->bind);
        self::$last_id   = $this->getCurrentDblink()->getLastId();
        self::$row_count = $this->getCurrentDblink()->getRowCount();
        $t2              = microtime(true);
        self::$error     = $this->getCurrentDblink()->getError();

        if (C('SHOW_SQL')) {
            $sql = '[' . self::$sql_count . ']' . strtr($this->sql, $this->debug_bind);
            Log::write(utf8togbk($sql) . "[Exec:" . ($t2 - $t1) . "s]", Log::SQL);
            $t2 - $t1 >= 0.1 && Log::write(strtr($this->sql, $this->debug_bind) . "[Exec:" . ($t2 - $t1) . "s]", '/dev/shm/slow_sql.cutlog');
        }
        if ($result === false || self::$error) {
            Log::write('[' . self::$sql_count . ']' . strtr($this->sql, $this->debug_bind) . "[Exec:" . ($t2 - $t1) . "s]", Log::ERR);
            Log::write(Log::COLOR_ERROR . self::$error . Log::COLOR_END, Log::ERR);

            return false;
        } else {
            return $result;
        }
    }

    /**
     * readCache @desc 读取缓存
     *
     * @author wangjian
     * @return array|bool|string
     */
    private function readCache()
    {
        return McacheFactory::provide()->get(
            self::MEMCACHE_KEY_PRE . $this->expire_pre . md5($this->sql . serialize($this->bind))
        );
    }

    /**
     * writeCache @desc 写入缓存
     *
     * @author wangjian
     *
     * @param array $data 缓存内容
     */
    private function writeCache($data)
    {
        $key = self::MEMCACHE_KEY_PRE . $this->expire_pre . md5($this->sql . serialize($this->bind));
        if ($this->expire === -1) {
            McacheFactory::provide()->set($key, $data);
        } elseif ($this->expire === true) {
            McacheFactory::provide()->set($key, $data);
        } elseif ($this->expire > 0) {
            McacheFactory::provide()->set($key, $data, $this->expire);
        }
    }

    /**
     * getSql @desc getSql
     *
     * @author wangjian
     *
     * @param string $type SQL类型
     *
     * @return string
     */
    public function getSql($type = self::SELECT_DB)
    {
        $this->getTableInfo();
        $this->parseSql($type);

        return strtr($this->sql, $this->debug_bind);
    }

    /**
     * find @desc 找出查找结果的第一条数据
     *
     * @author wangjian
     * @return array|bool|mixed|string
     */
    public function find()
    {
        $this->limit(0, 1);
        $this->parseSql();

        return $this->_query(true);
    }

    /**
     * count @desc count
     *
     * @author wangjian
     * @return int
     */
    public function count()
    {
        $this->fields = 'count(1) as C';

        return (int)$this->getField('C');
    }

    /**
     * getField @desc 获取结果数组的某个字段
     *
     * @author wangjian
     *
     * @param string $field 字段名称
     *
     * @return mixed|null
     */
    public function getField($field)
    {
        $data = $this->find();
        if ($data && isset($data[$field])) {
            return $data[$field];
        } else {
            return null;
        }
    }

    /**
     * field @desc 需要查找的字段
     *
     * @author wangjian
     *
     * @param string $data 查找的字段信息
     *
     * @return $this
     */
    public function field($data = '*')
    {
        if (is_array($data)) {
            $this->fields = implode(',', $data);
        } elseif (is_string($data)) {
            $this->fields = $data;
        } else {
            $this->fields = '*';
        }

        return $this;
    }

    /**
     * query @desc 执行sql
     *
     * @author wangjian
     *
     * @param string $sql sql语句
     *
     * @return array|bool|mixed|string
     */
    public function query($sql)
    {
        $this->sql = $sql;

        return $this->_query();
    }

    /**
     * getError @desc 获取错误信息
     *
     * @author wangjian
     * @return string
     */
    static public function getError()
    {
        return self::$error;
    }

    /**
     * parseWhere @desc 格式化查询条件
     *
     * @author wangjian
     *
     * @param mixed $data array('id')
     */
    private function parseWhere($data)
    {
        if (!$data) {
            return;
        }
        $this->where = ' WHERE ';
        if (is_array($data)) {
            $logic = isset($data[self::LOGIC_WHERE]) && strtoupper($data[self::LOGIC_WHERE]) == 'OR' ? 'OR' : 'AND';
            unset($data[self::LOGIC_WHERE]);
            foreach ($data as $field => $value) {
                if ($field === self::STRING_WHERE) {
                    $this->where .= " " . $value . " {$logic}";
                    continue;
                }
                if (strpos($field, '.') > 0) {
                    list($tableAlias, $bindField) = explode('.', $field);
                    if (!is_null($tableAlias)) {
                        $tableAlias .= '.';
                    }
                } else {
                    $tableAlias = '';
                    $bindField  = $field;
                }

                if (is_array($value)) {
                    if (isset($value[self::EXP_WHERE]) && isset($value[self::VALUE_WHERE])) {
                        $exp       = $value[self::EXP_WHERE];
                        $bindValue = $value[self::VALUE_WHERE];
                    } else {
                        list($exp, $bindValue) = $value;
                    }

                    if ($bindValue === '' || is_null($bindValue)) {
                        $bindValue = "''";
                    }
                    if (!in_array($exp, self::$_exp)) {
                        $exp = '=';
                    }
                    if ($exp == 'exp') {
                        $exp = '';
                    }
                    $this->where .= " {$tableAlias}`{$bindField}` {$exp} {$bindValue} {$logic}";
                } else {

                    $this->where .= " {$tableAlias}`{$bindField}`=:{$bindField} {$logic}";

                    $this->bind[$bindField]             = $value;
                    $this->debug_bind[':' . $bindField] = '"' . addslashes($value) . '"';
                }
            }
            $this->where = substr($this->where, 0, strlen($this->where) - strlen($logic));
        } elseif (is_string($data)) {
            $this->where .= $data;
        }
    }

    /**
     * parseSql @desc 解析SQL
     *
     * @author wangjian
     *
     * @param string $type SQL类型
     */
    private function parseSql($type = self::SELECT_DB)
    {
        switch ($type) {
            case self::DELETE_DB:
                if ($this->where) {
                    $this->sql = "DELETE FROM `" . $this->tableName . "` " . $this->where;
                } else {
                    $this->sql = '';
                }
                break;
            case self::INSERT_DB:
                if ($this->data) {
                    $insertSql = $this->parseInsertData();
                    if (false == $insertSql) {
                        self::$error .= ' 没有要插入的数据，失败~' . var_export($this->data, 'true');
                        Log::write(self::$error, Log::ERR);

                        return;
                    }
                    $this->sql = 'INSERT INTO `' . $this->tableName . '`' . $insertSql;
                } else {
                    self::$error = '没有要插入的数据，失败~';
                    Log::write(self::$error, Log::ERR);

                    return;
                }
                break;
            case self::REPLACE_DB:
                if ($this->data) {
                    $insertSql = $this->parseInsertData();
                    if (false == $insertSql) {
                        self::$error .= '没有要插入的数据，失败~';
                        Log::write(self::$error, Log::ERR);

                        return;
                    }
                    $this->sql = 'REPLACE INTO `' . $this->tableName . '`' . $insertSql;
                } else {
                    self::$error = '没有要插入的数据，失败~';
                    Log::write(self::$error, Log::ERR);

                    return;
                }
                break;
            case self::UPDATE_DB:
                if ($this->data) {
                    $updateSql = $this->parseUpdateData();
                    if (false === $updateSql) {
                        self::$error .= ' 没有更新的数据，更新失败~';
                        Log::write(self::$error, Log::ERR);

                        return;
                    }
                    $this->sql = 'UPDATE `' . $this->tableName . '` SET ' . $updateSql;
                }
                break;
            default:

                $this->sql = 'SELECT ' . $this->fields . " FROM " . $this->tableName . " " . $this->where . " "
                    . $this->order
                    . $this->limit;

        }

        APP_DEBUG || error_log($this->sql . PHP_EOL, 3, C('LOG_PATH_PARSE_SQL'));
    }

    /**
     * delete @desc 删除
     *
     * @author wangjian
     * @return bool
     */
    public function delete()
    {
        $this->parseSql(self::DELETE_DB);

        return $this->_execute();
    }

    /**
     * cache @desc 设置缓存
     *
     * @author wangjian
     *
     * @param bool   $cache
     * @param string $expire_pre
     *
     * @return $this
     */
    public function cache($cache = false, $expire_pre = '')
    {
        $this->expire     = $cache;
        $this->expire_pre = $expire_pre;

        return $this;
    }

    /**
     * insert @desc 插入数据
     *
     * @author wangjian
     *
     * @param bool $sql sql
     *
     * @return bool|null|string
     */
    public function insert($sql = false)
    {
        $this->getTableInfo();
        $this->parseSql(self::INSERT_DB);
        if ($sql === true) {
            return $this->sql;
        }

        if ($this->_execute()) {
            return self::$last_id;
        } else {
            return false;
        }
    }

    /**
     * replace @desc 替换
     *
     * @author wangjian
     * @return bool
     */
    public function replace()
    {
        $this->getTableInfo();
        $this->parseSql(self::REPLACE_DB);

        return $this->_execute();
    }

    /**
     * update @desc 更新
     *
     * @author wangjian
     * @return bool|int
     */
    public function update()
    {
        $this->getTableInfo();
        $this->parseSql(self::UPDATE_DB);
        if ($this->_execute()) {
            return self::$row_count;
        } else {
            return false;
        }
    }

    /**
     * execute @desc 执行sql
     *
     * @author wangjian
     *
     * @param $sql
     *
     * @return bool
     */
    public function execute($sql)
    {
        $this->sql = $sql;

        return $this->_execute();
    }

    /**
     * getLastId @desc 获取最后id
     *
     * @author wangjian
     * @return null
     */
    static public function getLastId()
    {
        return self::$last_id;
    }

    /**
     * getRowCount @desc 获取结果集数目
     *
     * @author wangjian
     * @return int
     */
    static public function getRowCount()
    {
        return self::$row_count;
    }

    /**
     * data @desc 填充字段
     *
     * @author wangjian
     *
     * @param string $data 要更新或者插入的数组
     *
     * @return $this
     */
    public function data($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * getTableInfo @desc 获取表字段信息
     *
     * @author wangjian
     * @return bool
     */
    private function getTableInfo()
    {
        static $dbinfo;
        if (!isset($dbinfo[$this->tableName])) {
            $info = F('Db/' . $this->tableName);
            if (!is_array($info) || $info['version'] != C('VERSION') || APP_DEBUG) {
                $info = $this->query('SHOW COLUMNS FROM `' . $this->tableName . '`');
                if ($info) {
                    $info['version'] = C('VERSION');
                    F('Db/' . $this->tableName, $info);
                } else {
                    Log::write('查询表信息失败~', Log::ERR);

                    return false;
                }
            }
            unset($info['version']);
            $dbinfo[$this->tableName] = $info;
        }

        foreach ($dbinfo[$this->tableName] as $field) {
            $this->db_fields[] = $field['Field'];
            if ($field['Key'] == 'PRI') {
                $this->pk = $field['Field'];
            }
            $this->db_fields_info[$field['Field']] = $field;
        }

        return true;
    }

    /**
     * parseUpdateData @desc 格式化更新数据
     *
     * @author wangjian
     * @return bool|string
     */
    private function parseUpdateData()
    {
        $update = [];
        if (empty($this->where)) {
            if (isset($this->data[$this->pk]) && $this->data[$this->pk]) {
                $this->where = ' WHERE ' . $this->pk . "='" . $this->data[$this->pk] . "'";
                unset($this->data[$this->pk]);
            } else {
                self::$error = '更新失败~没有条件约束';

                return false;
            }
        }
        foreach ($this->data as $k => $v) {
            if (in_array($k, $this->db_fields)) {

                if (!is_array($v)) {
                    if (is_null($v)) {
                        //判断值是否为空
                        if ($this->db_fields_info[$k]['Null'] == 'NO') {
                            //该字段设置不能为空
                            if (is_null($this->db_fields_info[$k]['Default'])) {
                                self::$error = "{$k}的值不能为空";

                                return false;
                            }
                        }
                    }

                    if (isset($this->bind[$k])) {
                        $bindk = $k . '_update';
                    } else {
                        $bindk = $k;
                    }
                    $update[]                       = "`" . $k . "`=:" . $bindk . "";
                    $this->bind[$bindk]             = $v;
                    $this->debug_bind[":" . $bindk] = '"' . addslashes($v) . '"';
                } else {
                    if (isset($v[0]) && $v[0] === 'exp') {
                        $update[] = "`" . $k . "`=" . $v[1];
                    }
                }
            }
        }

        if ($update) {
            return implode(',', $update) . $this->where;
        }

        return false;
    }

    /**
     * parseInsertData @desc 解析插入数据
     *
     * @author wangjian
     * @return bool|string
     */
    private function parseInsertData()
    {
        $tmpkeys   = [];
        $tmpvalues = [];
        foreach ($this->data as $k => $v) {
            if (in_array($k, $this->db_fields)) {
                //将插入的字段是否合法
                if (!is_array($v)) {
                    //非数组的情况
                    if (is_null($v)) {
                        //判断值是否为空
                        if ($this->db_fields_info[$k]['Null'] == 'NO') {
                            //该字段设置不能为空
                            if (is_null($this->db_fields_info[$k]['Default'])) {
                                self::$error = "{$k}的值不能为空";

                                return false;
                            }
                        }
                    }
                    $tmpkeys[]                  = '`' . $k . '`';
                    $tmpvalues[]                = ":" . $k;
                    $this->bind[$k]             = $v;
                    $this->debug_bind[":" . $k] = '"' . addslashes($v) . '"';
                } else {
                    if ($v[0] === 'exp') {
                        $tmpkeys[]   = '`' . $k . '`';
                        $tmpvalues[] = $v[1];
                    }
                }
            }
        }
        if ($tmpkeys) {
            return "(" . implode(',', $tmpkeys) . ")VALUES(" . implode(',', $tmpvalues) . ")";
        }

        return false;
    }

    /**
     * beginTransaction @desc 开启事务
     *
     * @author wangjian
     *
     * @param bool $throwException 是否抛出异常
     *
     * @return bool
     * @throws AppException
     */
    public function beginTransaction($throwException = false)
    {

        $rs = $this->getCurrentDblink()->beginTransaction();
        if ($throwException && !$rs) {
            throw new AppException('开始事务失败~', 99001);
        }

        return $rs;
    }

    /**
     * commit @desc 提交事务
     *
     * @author wangjian
     *
     * @param bool $throwException 是否抛出异常
     *
     * @return bool
     * @throws AppException
     */
    public function commit($throwException = false)
    {
        $rs = $this->getCurrentDblink()->commit();
        if ($throwException && !$rs) {
            throw new AppException("事务提交失败~", 99002);
        }

        return $rs;
    }

    /**
     * roolBack @desc 回滚事务
     *
     * @author wangjian
     * @return bool
     */
    public function roolBack()
    {
        return $this->getCurrentDblink()->roolBack();
    }
}