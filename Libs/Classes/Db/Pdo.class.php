<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/8 0008
 * Time: 下午 1:23
 */
namespace Clhapp\DB;

use Clhapp\AppException;

class Pdo
{
    /**
     * @var \PDO
     */
    private $link;

    private $config;
    private $dns = "{driver}:dbname={dbname};host={host};port={port};charset={charset}";
    private $option
        = [
            'must' => [
                'driver',
                'dbname',
                'host',
                'port',
                'charset',
                'username',
                'password',
            ],
        ];

    private $last_id = null;
    private $row_count = 0;

    /**
     * @var \PDOStatement
     */
    private $stmt;
    private $error = '';

    public function __construct($config)
    {
        $this->config = $config;

        $this->parseConfig();
    }

    protected function parseConfig()
    {
        foreach ($this->option['must'] as $k) {
            if (!isset($this->config[$k])) {
                throw new AppException('PDO 缺少配置' . $k . '参数');
            }
        }
        $this->dns = strtr(
            $this->dns,
            [
                '{driver}'  => $this->config['driver'],
                '{dbname}'  => $this->config['dbname'],
                '{host}'    => $this->config['host'],
                '{port}'    => $this->config['port'],
                '{charset}' => $this->config['charset'],
            ]
        );
        try {
            $this->link = new \PDO($this->dns, $this->config['username'], $this->config['password']);
            $this->link->setAttribute(\PDO::ATTR_TIMEOUT, 1);
            $this->link->setAttribute(\PDO::ATTR_AUTOCOMMIT, 1);
            $this->link->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new AppException(
                'Connection failed: ' . $e->getMessage() . "|$this->dns|" . var_export($this->config, true)
            );
        }
    }

    private function prepare($sql)
    {
        $this->error = '';
        $stmt        = $this->link->prepare($sql);
        if ($stmt === false) {
            $this->error = 'prepare false';

            return false;
        }
        $this->stmt = $stmt;

        return true;
    }

    /**
     * @param array $bind
     */
    public function bind($bind)
    {
        if (!is_array($bind)) {
            $bind = [];
        }
        foreach ($bind as $k => $v) {
            if (is_array($v)) {
                $this->stmt->bindValue($k, $v['value'], $v['type']);
            } else {
                $this->stmt->bindValue($k, $v);
            }
        }

        return $this->stmt->execute();
    }

    private function getStmtError()
    {
        $errorinfo = $this->stmt->errorInfo();
        if (!is_null($errorinfo[1])) {
            $this->error = implode("\t", $errorinfo);
        }
    }

    public function find($sql, $bind = [])
    {
        if (!$this->prepare($sql)) {
            return false;
        }
        if (!$this->bind($bind)) {
            $this->getStmtError();

            return false;
        }
        $data = $this->stmt->fetch();

        $this->getStmtError();

        return $data;
    }

    public function select($sql, $bind = [])
    {
        if (!$this->prepare($sql)) {
            return false;
        }
        if (!$this->bind($bind)) {
            $this->getStmtError();

            return false;
        }
        $data = $this->stmt->fetchAll();

        $this->getStmtError();

        return $data;
    }

    public function exec($sql, $bind = [])
    {
        if (!$this->prepare($sql)) {
            return false;
        }
        if (!$this->bind($bind)) {
            $this->getStmtError();

            return false;
        }
        $this->last_id   = $this->link->lastInsertId();
        $this->row_count = $this->stmt->rowCount();

        return true;
    }

    public function getLastId()
    {
        return $this->last_id;
    }

    public function getRowCount()
    {
        return $this->row_count;
    }

    public function getError()
    {
        return $this->error;
    }

    public function beginTransaction()
    {
        return $this->link->beginTransaction();
    }

    public function commit()
    {
        return $this->link->commit();
    }

    public function roolBack()
    {
        return $this->link->rollBack();
    }
}