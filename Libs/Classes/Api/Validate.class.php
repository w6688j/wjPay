<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/17
 * Time: 0:09
 */

namespace Clhapp\Api;

class Validate
{
    /**
     * isIp2Long @desc
     *
     * @author wangjian
     *
     * @param string $ip ip
     *
     * @return bool
     */
    public static function isIp2Long($ip)
    {
        return (bool)preg_match('#^-?[0-9]+$#', (string)$ip);
    }

    /**
     * isIp @desc 是否是ip
     *
     * @author wangjian
     *
     * @param string $ip ip
     *
     * @return bool
     */
    public static function isIp($ip)
    {
        return ip2long($ip) > 0;
    }

    /**
     * isAnything @desc 不验证
     *
     * @author wangjian
     * @return bool
     */
    public static function isAnything()
    {
        return true;
    }

    /**
     * Check for MD5 string validity
     *
     * @param string $md5 MD5 string to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isMd5($md5)
    {
        return (bool)preg_match('/^[a-f0-9A-F]{32}$/', $md5);
    }

    /**
     * Check for SHA1 string validity
     *
     * @param string $sha1 SHA1 string to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isSha1($sha1)
    {
        return (bool)preg_match('/^[a-fA-F0-9]{40}$/', $sha1);
    }

    /**
     * isPhone @desc 是否是一个合法的手机号码
     *
     * @author wangjian
     *
     * @param string $phone 手机号码
     *
     * @return bool
     */
    public static function isPhone($phone)
    {
        return (bool)preg_match('/^1[0-9]{10}$/', $phone);
    }

    /**
     * Check for a float number validity
     *
     * @param float $float Float number to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isFloat($float)
    {
        return strval((float)$float) == strval($float);
    }

    public static function isUnsignedFloat($float)
    {
        return strval((float)$float) == strval($float) && $float >= 0;
    }

    /**
     * Check for price validity
     *
     * @param string $price Price to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isPrice($price)
    {
        return (bool)preg_match('/^[0-9]{1,10}(\.[0-9]{1,9})?$/', $price);
    }

    /**
     * isJson @desc 验证JSON
     *
     * @author wangjian
     *
     * @param string $jsonStr JSON字符串
     *
     * @return bool
     */
    public static function isJson($jsonStr)
    {
        if (!is_string($jsonStr)) {
            return false;
        }

        return is_array(json_decode($jsonStr, true));
    }

    /**
     * Check for date validity
     *
     * @param string $date Date to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isDate($date)
    {
        return strtotime($date) !== false;
    }

    /**
     * isLiveDate @desc 是否是合法的入住时间
     *
     * @author wangjian
     *
     * @param string $date 时间
     *
     * @return bool
     */
    public static function isLiveDate($date)
    {
        if (!preg_match('/^([0-9]{4})\/((?:0?[0-9])|(?:1[0-2]))\/((?:0?[0-9])|(?:[1-2][0-9])|(?:3[01]))$/', $date, $matches)) {
            return false;
        }

        return checkdate((int)$matches[2], (int)$matches[3], (int)$matches[1]);
    }

    /**
     * Check for birthDate validity
     *
     * @param string $date birthdate to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isBirthDate($date)
    {
        if (empty($date) || $date == '0000-00-00') {
            return true;
        }
        if (preg_match('/^([0-9]{4})-((?:0?[1-9])|(?:1[0-2]))-((?:0?[1-9])|(?:[1-2][0-9])|(?:3[01]))([0-9]{2}:[0-9]{2}:[0-9]{2})?$/', $date, $birth_date)) {
            if ($birth_date[1] > date('Y') && $birth_date[2] > date('m') && $birth_date[3] > date('d')
                || $birth_date[1] == date('Y') && $birth_date[2] == date('m') && $birth_date[3] > date('d')
                || $birth_date[1] == date('Y') && $birth_date[2] > date('m')
            ) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Check for boolean validity
     *
     * @param bool $bool Boolean to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isBool($bool)
    {
        return $bool === null || is_bool($bool) || preg_match('/^(0|1)$/', $bool);
    }

    /**
     * Check for an integer validity
     *
     * @param int $value Integer to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isInt($value)
    {
        return ((string)(int)$value === (string)$value || $value === false);
    }

    /**
     * Check for an integer validity (unsigned)
     *
     * @param int $value Integer to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isUnsignedInt($value)
    {
        return ((string)(int)$value === (string)$value && $value < 4294967296 && $value >= 0);
    }

    /**
     * Check for an percentage validity (between 0 and 100)
     *
     * @param float $value Float to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isPercentage($value)
    {
        return (Validate::isFloat($value) && $value >= 0 && $value <= 100);
    }

    /**
     * Check object validity
     *
     * @param object $object Object to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isLoadedObject($object)
    {
        return is_object($object) && $object->id;
    }

    /**
     * Check object validity
     *
     * @param string $color 颜色
     *
     * @return bool Validity is ok or not
     */
    public static function isColor($color)
    {
        return (bool)preg_match('/^(#[0-9a-fA-F]{6}|[a-zA-Z0-9-]*)$/', $color);
    }

    /**
     * Check if URL is absolute
     *
     * @param string $url URL to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isUrl($url)
    {
        if (!empty($url)) {
            return preg_match('/^(https?:)?\/\/[$~:;#,%&_=\(\)\[\]\.\? \+\-@\/a-zA-Z0-9]+$/', $url);
        }

        return true;
    }

    /**
     * Check for standard name file validity
     *
     * @param string $name Name to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isFileName($name)
    {
        return (bool)preg_match('/^[a-zA-Z0-9_.-]+$/', $name);
    }

    /**
     * Check if $data is a PrestaShop cookie object
     *
     * @param mixed $data to validate
     *
     * @return bool
     */
    public static function isCookie($data)
    {
        return (is_object($data) && get_class($data) == 'Cookie');
    }

    /**
     * Price display method validity
     *
     * @param string $data Data to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isString($data)
    {
        return is_string($data);
    }

    /**
     *
     * @param array $ids
     *
     * @return bool return true if the array contain only unsigned int value
     */
    public static function isArrayWithIds($ids)
    {
        if (count($ids)) {
            foreach ($ids as $id) {
                if ($id == 0 || !Validate::isUnsignedInt($id)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * isNumberStr @desc 判断是否是数字间隔逗号构成的字符串
     *
     * @author wangjian
     *
     * @param string $str 验证字符串
     *
     * @return bool
     */
    public static function isNumberStr($str)
    {
        return (bool)preg_match('/^([0-9]+_)+$/', $str . '_');
    }

    /**
     * isNumberJoinedByComma @desc
     *
     * @author wangjian
     *
     * @param $str
     *
     * @return bool
     */
    public static function isNumberJoinedByComma($str)
    {
        return (bool)preg_match('/^([0-9]+,)+$/', $str . ',');
    }
}