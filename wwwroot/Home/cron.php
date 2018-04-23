<?php

/**
 * Created by PhpStorm.
 * User: wangjian
 * Date: 2017/2/25
 * Time: 20:25
 */
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);

class cron
{
    const TOKEN = 'd2460822677bbeef';

    protected $conf;
    protected $pid;

    public function __construct()
    {
        if ($_GET['token'] != self::TOKEN) {
            echo '非法';
            die();
        }
        $this->init();
    }

    private function init()
    {
        $this->conf = include dirname(dirname(__DIR__)) . '/Crontab/Conf/conf.php';
    }

    public function status()
    {
        if ($this->isRunning()) {
            echo '<html style="padding: 10px;">';
            echo "<h3>crontab[pid:{$this->pid}] is running...</h3>";

            echo $this->getMemInfo();

            echo $this->getDiskInfo();

            echo '<ul style="list-style: none;padding: 0;margin: 30px 0;">';
            foreach ($this->log() as $log) {
                echo "<li>" . htmlspecialchars($log) . '</li>';
            }
            echo '</ul>';
        } else {
            echo 'crontab is stoped...';
        }
    }

    protected function getDiskInfo()
    {
        exec('df -h', $output);
        $html = "<h3>Disk:</h3>";
        $html .= "<table width='800' border='1' style='border-spacing: 0;'>";

        foreach ($output as $line) {
            $html .= "<tr>";
            foreach ((explode(" ", $line)) as $v) {
                if ($v == 'on') continue;
                if (empty($v) && $v != '0') {
                    continue;
                }
                $html .= "<td>{$v}</td>";
            }
            $html .= "</tr>";
        }

        $html .= "</table>";

        return $html;
    }

    protected function getMemInfo()
    {
        exec('free -m', $output);
        $html = "<h3>Memory:</h3>";
        $html .= "<table width='800' border='1' style='border-spacing: 0;'>";
        $html .= "<tr>";
        $html .= "<td></td>";
        foreach (array_filter(explode(" ", $output[0])) as $v) {
            $html .= "<td>{$v}</td>";
        }
        $html .= "</tr><tr>";
        foreach (array_filter(explode(" ", $output[1])) as $v) {
            $html .= "<td>{$v}</td>";
        }
        $html .= "</tr><tr>";
        $html .= "<td colspan='10'>" . $output[2] . "</td>";

        $html .= "</tr><tr>";
        $html .= "<td colspan='10'>" . $output[3] . "</td>";

        $html .= "</tr></table>";

        return $html;
    }

    protected function log()
    {
        $cmd = "tail " . $this->conf['log'] . ' -n100';

        exec($cmd, $output);

        return array_reverse(array_filter($output));
    }

    public function start()
    {
        if ($this->isRunning()) {
            echo "crontab is running,please stop first..";
        } else {
            if ($this->run()) {
                echo 'crontab start...[ok]';
            } else {
                echo 'crontab start...[failed]';
            }
        }
    }

    /**
     * @desc   stop 关闭
     * @author wangjian
     */
    public function stop()
    {
        if ($this->isRunning()) {
            if ($this->_stop()) {
                echo 'crontab stoped...[ok]';
            } else {
                echo 'crontab stoped...[failed]';
            }
        } else {
            echo "crontab is already stoped...";
        }
    }

    private function _stop()
    {
        unlink($this->conf['pid_path']);

        return !file_exists($this->conf['pid_path']);
    }

    /**
     * @desc   run 启动定时任务
     * @author wangjian
     * @return bool
     */
    private function run()
    {
        $cmd = "/usr/local/php7/bin/php " . dirname(dirname(__DIR__)) . "/Crontab/main.php > " . $this->conf['log'] . ' &';
        echo $cmd . '<br>';
        exec($cmd, $output);
        sleep(1);

        return $this->isRunning();
    }

    public function isRunning()
    {
        if (!file_exists($this->conf['pid_path'])) {
            return false;
        }
        $content = file_get_contents($this->conf['pid_path']);
        list($this->pid,) = explode('|', $content);

        if ($this->pid) {
            exec("ps -eo pid | grep {$this->pid }", $output);
            if ($output) {
                return true;
            }
        }

        return false;
    }

    public function help()
    {
        echo 'start|stop|restart|status';
    }

    public function reconfigure()
    {
        if (!$this->isRunning()) {
            $this->status();
            exit();
        }
        file_put_contents($this->conf['pid_path'], $this->pid . '|reconfigure');
        $i = 0;
        while ($i++ <= 10) {
            sleep(1);
            $content = file_get_contents($this->conf['pid_path']);
            if ($content == $this->pid) {
                break;
            }
        }
        $this->status();
    }
}

$obj = new cron();
switch ($_GET['act']) {
    case 'start':
        $obj->start();
        break;
    case 'stop':
        $obj->stop();
        break;
        case 'restart':
        $obj->stop();
        $obj->start();
        break;
    case 'reconfigure':
        $obj->reconfigure();
        break;
    case 'status':
        $obj->status();
        break;
    default:
        $obj->help();

}
