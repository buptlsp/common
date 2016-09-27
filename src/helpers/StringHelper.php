<?php
namespace lspbupt\common\helpers;
use Yii;
use yii\helpers\ArrayHelper;

class StringHelper extends \yii\helpers\StringHelper
{
    public static $password_blacklist = array("123456","123456789","000000","111111","123123","5201314","666666","123321","1314520","1234567890","888888","1234567","654321","12345678","520520","7758521","112233", "147258","123654","987654321","88888888","147258369","666888","5211314","521521","a123456","zxcvbnm","999999","222222","123123123","1314521","201314","woaini","789456","555555","qwertyuiop","100200","168168","qwerty","258369","456789","110110","789456123","159357","123789","123456a","121212","456123","987654","111222","1111111111","7758258","00000000","admin","administrator","333333","1111111","369369","888999","asdfgh","11111111","woaini1314","258258","0123456789","369258","aaaaaa","778899","0000000000","0000000","159753","abc123","585858","asdfghjkl","321654","211314","584520","abcdefg","777777","0123456","a123456789","123654789","abc123456","336699","abcdef","518518","888666","708904","135246","12345678910","147369","110119","qq123456","789789","251314","555666","111111111","123000","zxcvbn","qazwsx","123456abc", "hlj12345");

    //正则匹配一个电话是否为正确的电话号码
    public static function checkMobile($mobile)
    {
        if (preg_match("/^1[3-8]{1}\d{9}$/", $mobile)) {
            return true;
        }
        return false;
    }

    //正则匹配一个邮箱是否为正确的邮箱
    public static function checkEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
    }

    public static function checkLogin($login)
    {
        if(preg_match("/\w{5,10}/", $login)) {
            return true;
        }
        return false;
    }

    /*
     * 防止单条消息过长
     */
    public static function truncateMsg($msg, $len = 250)
    {
        $arridx = 0;
        $line = '';
        $subidx = 0;
        $count = 0;

        while ($subidx < strlen($msg)) {
            $uch = '';
            if ($count == $len - 2) {
                $line = $line . '..';
                break;
            }
            if ((ord($msg[$subidx]) & 0x80) == 0x00) {
                $uch .= $msg[$subidx];
                $subidx += 1;
                $count  += 1;
            } else if ((ord($msg[$subidx]) & 0xc0) == 0x80) {
                $subidx += 1;
                continue;
            } else if ((ord($msg[$subidx]) & 0xe0) == 0xc0) {
                $uch .= $msg[$subidx];
                $subidx += 1;
                $uch .= $msg[$subidx];
                $subidx += 1;
                $count  += 1;
            } else if ((ord($msg[$subidx]) & 0xf0) == 0xe0) {
                $uch .= $msg[$subidx];
                $subidx += 1;
                $uch .= $msg[$subidx];
                $subidx += 1;
                $uch .= $msg[$subidx];
                $subidx += 1;
                $count  += 1;
            } else if ((ord($msg[$subidx]) & 0xf8) == 0xf0) {
                $uch .= $msg[$subidx];
                $subidx += 1;
                $uch .= $msg[$subidx];
                $subidx += 1;
                $uch .= $msg[$subidx];
                $subidx += 1;
                $uch .= $msg[$subidx];
                $subidx += 1;
                $count  += 1;
            }

            $line .= $uch;
        }
        return $line;
    }

    public static function checkPasswdValid($password)
    {
        //判断密码长度
        if (empty($password) || strlen($password) < 6 || strlen($password) > 16) {
            return "密码长度应该在6-16位之间";
        }
        if (in_array($password, self::$password_blacklist)) {
             return '您的密码过于简单';
        }
        return false;
    }

    // 判断ip是否在某个范围内
    // This function takes 2 arguments, an IP address and a "range" in several
    // different formats.
    // Network ranges can be specified as:
    // 1. Wildcard format:     1.2.3.*
    // 2. CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
    // 3. Start-End IP format: 1.2.3.0-1.2.3.255
    // The function will return true if the supplied IP is within the range.
    // Note little validation is done on the range inputs - it expects you to
    // use one of the above 3 formats.
    public static function isIPInRange($ip, $range)
    {
        if (strpos($range, '/') !== false) {
            // $range is in IP/NETMASK format
            list($range, $netmask) = explode('/', $range, 2);
            if (strpos($netmask, '.') !== false) {
                // $netmask is a 255.255.0.0 format
                $netmask = str_replace('*', '0', $netmask);
                $netmask_dec = ip2long($netmask);
                return ((ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec));
            } else {
                // $netmask is a CIDR size block
                // fix the range argument
                $x = explode('.', $range);
                while (count($x) < 4)
                    $x[] = '0';
                list($a, $b, $c, $d) = $x;
                $range = sprintf("%u.%u.%u.%u", empty($a) ? '0' : $a, empty($b) ? '0' : $b, empty($c) ? '0' : $c, empty($d) ? '0' : $d);
                $range_dec = ip2long($range);
                $ip_dec = ip2long($ip);

                # Strategy 1 - Create the netmask with 'netmask' 1s and then fill it to 32 with 0s
                #$netmask_dec = bindec(str_pad('', $netmask, '1') . str_pad('', 32-$netmask, '0'));

                # Strategy 2 - Use math to create it
                $wildcard_dec = pow(2, (32 - $netmask)) - 1;
                $netmask_dec = ~$wildcard_dec;

                return (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
            }
        } else if (strpos($range, '*') !== false || strpos($range, '-') !== false) {
            // range might be 255.255.*.* or 1.2.3.0-1.2.3.255
            if (strpos($range, '*') !== false) { // a.b.*.* format
                // Just convert to A-B format by setting * to 0 for A and 255 for B
                $lower = str_replace('*', '0', $range);
                $upper = str_replace('*', '255', $range);
                $range = "$lower-$upper";
            }

            if (strpos($range, '-') !== false) { // A-B format
                list($lower, $upper) = explode('-', $range, 2);
                $lower_dec = (float)sprintf("%u", ip2long($lower));
                $upper_dec = (float)sprintf("%u", ip2long($upper));
                $ip_dec = (float)sprintf("%u", ip2long($ip));
                return (($ip_dec >= $lower_dec) && ($ip_dec <= $upper_dec));
            }
            return false;
        } else {
            return $ip == $range;
        }
    }

     /**
      * 是否为公司内部IP
      * @param String $ip
      * @return bool
      */
     public static function isInnerIP($ip = null)
     {
         $ip == null && $ip = Yii::$app->request->userIP;
         $INNER_IP_ADDR = ArrayHelper::getValue(Yii::$app->params, "inner_iplist");
         foreach ($INNER_IP_ADDR as $range) {
             if (self::isIPInRange($ip, $range)) {
                 return true;
             }
         }
         return false;
     }

    /**
     * 得到加星号的手机号 133****4444
     * @params $number 手机号
     * @return String 加过星号的
     */
     public static function getMaskMobile($number)
     {
         $masked = substr_replace($number, '****', 3, 4);
         return $masked;
     }

     //每四位加一个空格，方便显示
     public static function formatCode($str)
     {
         $ret = "";
         for($i = 0; $i < strlen($str); $i++) {
            if($i%4 == 0) {
                $ret .= " ";
            }
            $ret .= $str[$i];
         }
         return trim($ret);
     }

    //显示日期
    public static function showDate($timestamp)
    {
        if(empty($timestamp)) {
            return '-';
        }
        return date('Y-m-d', $timestamp);
    }

}
