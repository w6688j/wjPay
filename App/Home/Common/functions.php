<?php
/**
 * dateToEasyStr @desc 将时间字符串转化成易于理解的字符串
 *
 * @author wangjian
 *
 * @param string $date 日期
 *
 * @return string
 */
function dateToEasyStr($date)
{
    $timestamp = strtotime($date);
    if ($timestamp <= 0) {
        return 'undefined time :' . $date;
    } else {
        $space = time() - $timestamp;
        if ($space > 0) {
            $suffix = '前';
        } else {
            $space  = -$space;
            $suffix = '后';
        }
        if ($space <= 60) {
            return $space . '秒' . $suffix;
        } elseif ($space <= 3600) {
            return round($space / 60) . '分钟' . $suffix;
        } elseif ($space < 86400) {
            return round($space / 3600) . '小时' . $suffix;
        } elseif ($space < 2592000) {
            return round($space / 86400) . '天' . $suffix;
        } elseif ($space < 31104000) {
            return round($space / 2592000) . '月' . $suffix;
        } else {
            return round($space / 31104000) . '年' . $suffix;
        }
    }
}