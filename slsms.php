<?php

namespace SoufunLab\Framework {

    use \Exception;
    use SoufunLab\Framework\Web\SlRequest;
    use SoufunLab\Framework\Text\SlEncoding;

    /*
     * 发送短信报警（此类使用时需要审批，发送短信是需要计入成本的）
     * */

    class SlSms {

        //短信接口地址
        private static $smsUrl = "http://smss.interface.light.%s.com:8080/sendsms.php?mobie=%s&content=%s";

        /*
         * 发送短信
         * @param string $phone，手机号码
         * @param string $message,发送消息（自动截断为70个字符进行分段发送，message支持自动空格转换为下划线）
         * @param string $domain,域名切换使用soufun/fang
         * */

        public static function send($phone, $message, $domain = "soufun") {
            $message = str_replace(" ", "_", $message);
            $index = 0;
            $length = mb_strlen($message, SlEncoding::DefaultEncoding);
            while ($index < $length) {
                $current = "";
                if ($index + 70 > $length) {
                    $current = mb_substr($message, $index, $length - $index, SlEncoding::DefaultEncoding);
                } else {
                    $current = mb_substr($message, $index, 70, SlEncoding::DefaultEncoding);
                }


                $html = SlRequest::getHtml(sprintf(self::$smsUrl, $domain, $phone, iconv(SlEncoding::DefaultEncoding, "gbk", $current)), "gbk");

                if (strpos($html, "0000") == false) {
                    throw new Exception("发送失败！");
                }
                $index = $index + 70;
            }
        }

        /*
         * 发送短信（多个号码）
         * @param string $phone，手机号码
         * @param string $message,发送消息（自动截断为70个字符进行分段发送，message支持自动空格转换为下划线）
         * @param string $domain,域名切换使用soufun/fang
         * */

        public static function sendMore($phones, $message, $domain = "soufun") {
            foreach ($phones as $phone) {
                self::send($phone, $message,$domain);
            }
        }

    }

}