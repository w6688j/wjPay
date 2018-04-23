<?php
/**
 * Created by PhpStorm.
 * User: wangjian
 * Date: 2017/2/5
 * Time: 11:58
 */

namespace crontab;

class CronTask
{
    /** @var  string 定时任务名称 */
    protected $name;
    /** @var  CronTime */
    protected $cronTime;
    /** @var  string 运行参数 */
    protected $arg = '';
    /** @var  string  动作 */
    protected $action;
    /** @var  string 标准输出重定向地址 默认空设备 */
    protected $log = '/dev/shm/crontab.log';

    protected $runCount = 0;//执行次数

    /**
     * @var CronMain
     */
    protected $cronMain;

    /**
     * @var array 最近10运行的时间戳数组
     */
    protected $runTimes = [];

    /**
     * CronTask constructor.
     *
     * @param string $taskName 任务名称
     * @param string $crontab  定时任务配置
     * @param string $arg      定时任务执行参数
     */
    public function __construct($taskName, $crontab, $arg = '')
    {
        $this->setName($taskName)
            ->setCronTime($crontab)
            ->setArg($arg);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name 任务名称
     *
     * @return CronTask
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return CronTime
     */
    public function getCronTime()
    {
        return $this->cronTime;
    }

    /**
     * @return string
     */
    public function getArg()
    {
        return $this->arg;
    }

    /**
     * @param string $arg
     */
    public function setArg($arg)
    {
        $this->arg = $arg;
    }

    /**
     * @param string $cronTimeStr
     *
     * @return CronTask
     */
    public function setCronTime($cronTimeStr)
    {
        $this->cronTime = CronTime::get($cronTimeStr);

        return $this;
    }

    /**
     * @param CronMain $cronMain
     *
     * @return CronTask
     */
    public function setCronMain($cronMain)
    {
        $this->cronMain = $cronMain;

        return $this;
    }

    /**
     * @desc   run
     * @author wangjian
     */
    public function run()
    {
        if ($this->cronTime->check() && !$this->hasRuned()) {
            $this->cronMain->log('run ' . $this->name);
            $cmd = sprintf("%s %s a=%s %s >> %s"
                    , $this->cronMain->getPhpBin()
                    , $this->cronMain->getRunFile()
                    , $this->action
                    , $this->arg
                    , $this->log
                ) . ' &';
            exec($cmd);
            $this->cronMain->log($cmd);
            $this->runCount++;
        }
    }

    /**
     * @desc   hasRuned 判断当前时间戳是否已经执行过
     * @author wangjian
     * @return bool
     */
    protected function hasRuned()
    {
        //去掉秒 时间戳精确到分钟
        $timestamp = strtotime(date('Y-m-d H:i:00'));
        if (in_array($timestamp, $this->runTimes)) {
            return true;
        } else {
            array_push($this->runTimes, $timestamp);
            if (count($this->runTimes) > 10) {
                array_shift($this->runTimes);
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     *
     * @return CronTask
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return string
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @param string $log
     *
     * @return CronTask
     */
    public function setLog($log)
    {
        $this->log = $log;

        return $this;
    }

    /**
     * @desc   getLastRunTime 获取上次执行时间
     * @author wangjian
     * @return string
     */
    public function getLastRunTime()
    {
        if (!$this->runTimes) {
            return '-';
        }

        return date('y-m-d H:i', end($this->runTimes));
    }

    /**
     * @desc   getNextRunTime 获取下次执行时间
     * @author wangjian
     * @return string
     */
    public function getNextRunTime()
    {
        $now = time() + 60;
        $i   = 0;
        while ($i++ < 10000) {
            if ($this->cronTime->check($now)) {
                break;
            }
            $now += 60;
        }

        return date('y-m-d H:i', $now);
    }

    /**
     * @desc   getRunCount 获取执行次数
     * @author wangjian
     * @return int
     */
    public function getRunCount()
    {
        return $this->runCount;
    }

}