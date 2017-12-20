<?php
/**
 * 微信服务器交互的内容处理.
 */

namespace Module;

/**
 * Class \Module\Weixin.
 */
class Weixin extends \Module\Base
{

    /**
     * Get instance of the derived class.
     *
     * @return static
     */
    public static function Instance()
    {
        $className = get_called_class();
        if (!isset(self::$instances[$className])) {
            self::$instances[$className] = new $className;
        }
        return self::$instances[$className];
    }

    /**
     * 消息解密（开启加解密功能时）.
     *
     * @param string $postStr      微信事件内容.
     * @param string $msgSignature 消息签名.
     * @param string $timestamp    时间戳.
     * @param string $nonce        随机字符.
     * 
     * @return mixed
     */
    public function decryption($postStr, $msgSignature, $timestamp, $nonce)
    {
        $msg = "";
        $weixinInfo = \Config\Common::$weixinInfo;
        // 启用加解密功能（即选择兼容模式或安全模式）
        if ($weixinInfo['is_safe_mode'] && !empty($weixinInfo['encoding_aes_key'])) {
            require_once (ROOT_PATH . 'Ext/weixin/message/wxBizMsgCrypt.php');

            $crypt = new \WXBizMsgCrypt($weixinInfo['token'], $weixinInfo['encoding_aes_key'], $weixinInfo['app_id']);
            $errCode = $crypt->decryptMsg($msgSignature, $timestamp, $nonce, $postStr, $msg);
            if ($errCode != 0) {
                $this->log("errcode:" . $errCode . "|" . $postStr, "weixinMsgDecryptFail");
            }
        } else {
            $msg = $postStr;
        }
        $this->log($msg, "weixinMsgEvents");

        return $msg;
    }

    /**
     * 消息加密（开启加解密功能时）.
     *
     * @param string $text 消息内容.
     *
     * @return mixed
     */
    public function encryption($text)
    {
        $msg = "";
        $weixinInfo = \Config\Common::$weixinInfo;
        // 启用加解密功能（即选择兼容模式或安全模式）
        if ($weixinInfo['is_safe_mode'] && !empty($weixinInfo['encoding_aes_key'])) {
            require_once (ROOT_PATH . 'Ext/weixin/message/wxBizMsgCrypt.php');

            $crypt = new \WXBizMsgCrypt(self::$weixinInfo['token'], $weixinInfo['encoding_aes_key'], $weixinInfo['app_id']);
            $timestamp = time();
            $nonce = \Util\Helper::createNonceStr();
            $errCode = $crypt->encryptMsg($text, $timestamp, $nonce, $msg);
            if ($errCode != 0) {
                $this->log("errcode:" . $errCode . "|" . $text, "weixinMsgEncryptFail");
            }
        } else {
            $msg = $text;
        }

        return $msg;
    }

    /**
     * 消息去重.
     * 
     * @param string $key       Key.
     * @param string $eventType 事件类型.
     * 
     * @return boolean
     */
    public function checkRepeat($key, $eventType = '')
    {
        // 其他类型消息不去重
        if (empty($eventType) || !in_array($eventType, array('subscribe', 'unsubscribe', 'scan', 'message'))) {
            return false;
        }
        $redis = \Module\Redis::Instance()->getRedis();
        $code = $redis->get($key);
        if (empty($code)) {
            $redis->setex($key, 120, 1);
            return false;
        } else {
            $this->log($key, 'weixinMsgEventRepeat');
            return true;
        }
    }

}
