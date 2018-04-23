<?php
/**
 * Created by PhpStorm.
 * User: wangjian
 * Date: 2017/2/5
 * Time: 11:32
 */

namespace crontab;

use crontab\log\CronLog;
use \Exception;

class CronMain
{
    protected $start_time;//定时任务开启的时间
    protected $php_bin;//php 所在文件
    protected $run_file; //执行文件路径
    protected $log_path;//定时任务日志文件 默认黑洞
    protected $pid_path;//保存进程id的文件
    protected $pid;
    protected $heart_space = 3600;
    protected $last_time;

    /** @var  Logger */
    protected $logger;
    /** @var CronTask[] */
    protected $cronTasks = [];
    //定时任务数量
    protected $cronTaskCount = 0;
    protected $options = [
        'php_bin'   => 'php',
        'log'       => '/dev/null',
        'pid_path'  => '/tmp/crontab.pid',
        'run_file'  => '',
        'namespace' => '',
        'paths'     => [],
        'tasks'     => [
            [
                //该任务状态 true：开启 false:关闭
                'status'  => false,
                //该任务描述
                'name'    => 'demo',
                //该任务名称
                'action'  => 'demoA',
                //该任务参数
                'arg'     => 'id=1',
                //该任务时间配置
                'crontab' => '*/1 * * * *',
                //该任务标准输出重定向
                'log'     => '/dev/null',
            ],
        ],
    ];

    protected $confFilePath;

    /**
     * crontabMain constructor.
     *
     * @param string $confFilePath 配置文件路径
     *
     * @throws Exception
     */
    public function __construct($confFilePath)
    {
        $this->confFilePath = $confFilePath;

        if (!file_exists($this->confFilePath)) {
            throw new \Exception("配置文件不存在");
        }
        $this->loadConf();
        $this->setLogger(new CronLog());
        set_exception_handler([$this, 'exceptionHandle']);
    }

    /**
     * loadConf @desc 加载配置文件
     *
     * @author wangjian
     * @return bool
     */
    protected function loadConf()
    {
        $conf = include $this->confFilePath;
        if (!is_array($conf)) {
            return false;
        }
        if (!$conf['tasks']) {
            return false;
        }

        $this->options = array_merge($this->options, $conf);
        $this->init();

        return true;
    }

    /**
     * init @desc 初始化
     *
     * @author wangjian
     */
    protected function init()
    {
        $this
            ->setRunFile($this->options['run_file'])
            ->setLogPath($this->options['log'])
            ->setPhpBin($this->options['php_bin'])
            ->setPidPath($this->options['pid_path'])
            ->setHeartSpace($this->options['heart_space']);
    }

    /**
     * exceptionHandle @desc 处理错误
     *
     * @author wangjian
     *
     * @param \Exception $e
     */
    public function exceptionHandle($e)
    {
        $error = sprintf("[%s] %s @%s +%s"
            , $e->getCode()
            , $e->getMessage()
            , $e->getFile()
            , $e->getLine());
        $this->log($error);
        echo "\033[1;40;31m" . $error . "\e[0m\n";
        //删除pid文件
        $this->delPidFile();
    }

    /**
     * stop @desc 停止定时任务
     *
     * @author wangjian
     * @throws Exception
     */
    public function stop()
    {
        if (!is_file($this->pid_path)) {
            throw new Exception("pid_file is not exist");
        }
        if (!$this->delPidFile()) {
            throw new Exception("stop main process failed!");
        }
    }

    /**
     * delPidFile @desc 删除进程文件
     *
     * @author wangjian
     * @return bool
     */
    public function delPidFile()
    {
        return unlink($this->pid_path);
    }

    /**
     * createPidFile @desc 创建进程文件
     *
     * @author wangjian
     *
     * @param null $pid 进程号
     *
     * @throws Exception
     */
    private function createPidFile($pid = null)
    {

        if ($pid === null) {
            if (!function_exists('posix_getpid')) {
                throw new Exception("crontab need posix_getpid function");
            }
            $this->pid = posix_getpid();
        }

        if (!file_put_contents($this->pid_path, $this->pid)) {
            throw new Exception('pid_file_path can not write anything~');
        }
    }

    /**
     * start @desc 开启定时任务
     *
     * @author wangjian
     * @throws Exception
     */
    public function start()
    {
        $this->start_time = time();
        if (PHP_SAPI != 'cli') {
            throw new Exception("crontab must run in cli,actual is " . PHP_SAPI);
        }
        $this->createPidFile();
        $this->log('main process started!')
            ->log('main process pid:' . $this->pid);
        $this->parseTasks();
        $this->sendStartEmail();
        while (true) {
            foreach ($this->cronTasks as $crontabTask)
                $crontabTask->run();
            sleep(10);
            //每次循环前查看有没有
            if (!$this->isContinue()) {
                break;
            }
            $this->sendHeartBeat();
        }
        $this->log("main process[{$this->pid}] stopped!");
        $this->sendStopEmail();
    }

    /**
     * getHeartSpace @desc 获取心跳间隔
     *
     * @author wangjian
     * @return int
     */
    public function getHeartSpace()
    {
        return $this->heart_space;
    }

    /**
     * setHeartSpace @desc 设置心跳间隔
     *
     * @author wangjian
     *
     * @param int $heart_space 心跳间隔
     *
     * @return $this
     */
    public function setHeartSpace($heart_space)
    {
        $this->heart_space = max($heart_space, 3600);

        return $this;
    }

    /**
     * canHeart @desc 是否能进行心跳
     *
     * @author wangjian
     * @return bool
     */
    protected function canHeart()
    {
        if (is_null($this->last_time)) {

            return true;
        }
        if (time() - $this->last_time > $this->heart_space) {
            return true;
        }

        return false;
    }

    /**
     * sendHeartBeat @desc 发送心跳邮件
     *
     * @author wangjian
     */
    public function sendHeartBeat()
    {
        if (!$this->canHeart()) {
            return;
        }
        $this->last_time = time();
        $html            = "<table  width='1000' border='1' style='border-spacing: 0;'>";
        $html .= "<tr><th width='150'>配置</th><th>任务名称</th><th width='200'>上次执行</th>";
        $html .= "<th width='200'>下次执行</th><th width='80' style='text-align: center'>次数</th></tr>";
        foreach ($this->cronTasks as $cronTask) {
            $html .= '<tr>';
            $html .= "<td>" . $cronTask->getCronTime()->getConfig() . '</td>';
            $html .= "<td style='text-align: center'>" . $cronTask->getAction() . '-' . $cronTask->getName() . '</td>';
            $html .= "<td style='text-align: center'>" . $cronTask->getLastRunTime() . '</td>';
            $html .= "<td style='text-align: center'>" . $cronTask->getNextRunTime() . '</td>';
            $html .= "<td style='text-align: center'>" . $cronTask->getRunCount() . '</td>';
            $html .= '</tr>';
        }
        $html .= "</table>";
        $this->sendEmail($html, '任务动态');
    }

    /**
     * sendStartEmail @desc 发送定时任务开启邮件
     *
     * @author wangjian
     *
     * @param string $action 操作
     */
    protected function sendStartEmail($action = '启动成功')
    {
        $body = "<h3>定时任务[pid:{$this->pid}] " . $action . "</h3>";
        $body .= "<p>" . date('Y-m-d H:i:s') . "</p>";
        $body .= "<p>环境：" . ENV . "</p>";
        $body .= "<p>加载任务如下...</p>";
        $body .= "<ol>";
        foreach ($this->cronTasks as $cronTask) {
            $body .= "<li> <span style='width: 150px;'>[{$cronTask->getCronTime()->getConfig()}]</span>";
            $body .= "<span style='width: 200px;'>{$cronTask->getAction()} {$cronTask->getArg()} </span>";
            $body .= "{$cronTask->getName()}</li>";
        }
        $body .= "</ol>";
        $this->sendEmail($body, 'crontab-' . $action);
    }

    /**
     * sendStopEmail @desc 发送停止定时任务邮件
     *
     * @author wangjian
     */
    protected function sendStopEmail()
    {
        $body = "定时任务[{$this->pid}] 已经停止";
        $body .= "<br>" . date('Y-m-d H:i:s');
        $body .= "<br> 环境:" . ENV;
        $this->sendEmail($body, '定时任务已经停止');
    }

    /**
     * parseTasks @desc 解析任务
     *
     * @author wangjian
     * @throws Exception
     */
    protected function parseTasks()
    {
        foreach ($this->options['tasks'] as $task) {
            list($crontab, $action, $name) = $task;
            $newTask = new CronTask($name, $crontab);
            $newTask
                ->setCronMain($this)
                ->setAction($action);
            $this->cronTasks[] = $newTask;
            $this->cronTaskCount++;
            $this->log('load task ' . $newTask->getName());
        }
        $this->log('total tasks:' . $this->cronTaskCount);
        if ($this->cronTaskCount <= 0) {
            throw new Exception("没有设置定时任务");
        }
    }

    /**
     * isContinue @desc 检查定时任务是否要继续下去
     *
     * @author wangjian
     * @return bool
     */
    protected function isContinue()
    {
        if (!file_exists($this->pid_path)) {
            $this->log('pid file is not exist');

            return false;
        }
        $content = file_get_contents($this->pid_path);
        list($pid, $action) = explode('|', $content);
        if ($pid == $this->pid) {
            switch ($action) {
                case 'reconfigure':
                    $this->reconfigure();
                default:
            }

            return true;
        }
        $this->log('pid is not match~');

        return false;
    }

    /**
     * reconfigure @desc 重新加载配置
     *
     * @author wangjian
     */
    private function reconfigure()
    {
        //重新载入配置
        if (!$this->loadConf()) {
            //重新载入配置失败
            $this->log('重新载入配置 [failed]');
            $this->sendEmail('重新载入配置失败，请检查配置<br>' . ENV, '重载配置失败');
        } else {
            $this->log('重新载入配置 [ok]');
            $this->cronTasks     = [];
            $this->cronTaskCount = 0;
            $this->parseTasks();
            $this->sendStartEmail('重新载入配置成功');
        }
        $this->createPidFile();
    }

    /**
     * log @desc 记录日志
     *
     * @author wangjian
     *
     * @param  string $msg 日志内容
     *
     * @return CronMain
     */
    public function log($msg)
    {
        if ($this->logger) {
            $this->logger->write($msg);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPhpBin()
    {
        return $this->php_bin;
    }

    /**
     * @param string $php_bin
     *
     * @return CronMain
     * @throws Exception
     */
    public function setPhpBin($php_bin)
    {
        $this->php_bin = $php_bin;
        if (empty($this->php_bin)) {
            throw new Exception("php bin file can't be empty");
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getRunFile()
    {
        return $this->run_file;
    }

    /**
     * @param string $run_file
     *
     * @return CronMain
     */
    public function setRunFile($run_file)
    {
        $this->run_file = $run_file;

        return $this;
    }

    /**
     * @return string
     */
    public function getLogPath()
    {
        return $this->log_path;
    }

    /**
     * @param string $log_path
     *
     * @return CronMain
     */
    public function setLogPath($log_path)
    {
        $this->log_path = $log_path;

        return $this;
    }

    /**
     * @return string
     */
    public function getPidPath()
    {
        return $this->pid_path;
    }

    /**
     * @param string $pid_path
     *
     * @return CronMain
     */
    public function setPidPath($pid_path)
    {
        $this->pid_path = $pid_path;

        return $this;
    }

    /**
     * @return int
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param Logger $logger
     *
     * @return CronMain
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * sendEmail @desc 发送邮件
     *
     * @author wangjian
     *
     * @param  string $content 邮件正文内容
     * @param string  $subject 邮件主题
     */
    protected function sendEmail($content, $subject = '定时任务提醒')
    {
        $obj = new CronEmail();
        $obj->setSubject($subject);
        $obj->setContent($content);
        $obj->setAddress('953372680@qq.com');
        $obj->send();
    }
}