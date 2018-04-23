<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2017/2/21
 * Time: 23:57
 */

namespace Clhapp\Extend;

use Clhapp\Mail\sendcloud;
use Clhapp\View;

class HeartBeat
{
    static public function run()
    {
        $obj = new sendcloud();
        $obj->setSubject('服务器心跳');

        $viewObj = new View();

        $viewObj->assign('title', ENV);
        $viewObj->assign('memory', self::getMem());
        $viewObj->assign('disk', self::getDisk());
        $viewObj->assign('logs', self::getLog());
        $html = $viewObj->display(CLHAPP_PATH . '/Tpl/heart.tpl.html', false);

        $obj = new sendcloud();
        $obj->setSubject('服务器心跳');
        $obj->setContent($html);
        $obj->setAddress('953372680@qq.com');
        $obj->send2();
    }

    protected static function getLog()
    {
        exec('tail /dev/shm/crontab.log -n20', $output);

        return array_reverse(array_filter($output));

    }

    protected static function getDisk()
    {
        exec('df -h', $output);
        $info = [];
        foreach ($output as $k => $line) {
            $tr = [];
            $i  = 0;

            foreach ((explode(" ", $line)) as $v) {
                if ($v == 'on') continue;
                if (empty($v) && $v != '0') {
                    continue;
                }
                $i++;
                if ($i == 5) {
                    if ((int)$v > 75)
                        $v = '<span style="color: red;font-weight: bold;">' . $v . '</span>';
                }
                $tr[] = $v;
            }
            while ($i++ <= 5) {
                $tr[] = '';
            }
            $info[] = $tr;
        }

        return $info;
    }

    protected static function getMem()
    {
        exec('free -m', $output);
        $info = [];
        foreach ($output as $k => $line) {
            $tr = [];
            $i  = 1;
            if ($k == 0) {
                $tr[] = '';
                $i++;
            }
            foreach ((explode(" ", $line)) as $v) {
                if ($v == 'on') continue;
                if (empty($v) && $v != '0') {
                    continue;
                }
                $i++;
                $tr[] = $v;
            }
            while ($i++ <= 7) {
                $tr[] = '';
            }
            $info[] = $tr;
        }

        return $info;
    }

}