<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/4/19
 * Time: 21:38
 */

namespace Clhapp\Home\Action;

use Clhapp\AppException;

class AjaxAction extends MainAction
{
    /**
     * _empty
     *
     * @author root
     * @time   2018/4/19 22:17
     * @throws AppException
     */
    public function _empty()
    {
        list($dir, $file) = explode('/', ACTION_NAME);
        $dir       = ucfirst($dir);
        $className = ucfirst($file) . 'Ajax';

        if (!is_file(APP_PATH . '/Ajax/' . $dir . '/' . $className . EXT)) {
            throw new AppException("接口" . ACTION_NAME . '不存在~path' . APP_PATH . '/Ajax/' . $dir . '/' . $className . EXT, 404);
        }

        include_once APP_PATH . '/Ajax/' . $dir . '/' . $className . EXT;

        $className = 'Clhapp\\' . basename(APP_PATH) . '\\Ajax\\' . $dir . '\\' . $className;
        $ajaxObj   = new $className($this->userObj);
        if (method_exists($ajaxObj, 'run')) {
            $ajaxObj->run();
        } else {
            throw new AppException("该接口不存在run方法~", 500);
        }
    }
}