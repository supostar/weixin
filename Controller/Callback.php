<?php
/**
 * 微信服务器请求发送消息接收处理.
 */

/**
 * Controller_Callback.
 */
class Controller_Callback extends Controller_Base
{

    /**
     * 请求数据.
     *
     * @var array
     */
    public $data = array();

    /**
     * 消息接收处理.
     *
     * @return void
     */
    public function Action_Index()
    {
        $signature = $_REQUEST["signature"];
        $timestamp = $_REQUEST["timestamp"];
        $nonce = $_REQUEST["nonce"];
        $msgSignature = $_REQUEST['msg_signature'];
        if ($this->checkSignature($signature, $timestamp, $nonce)) {
            $echostr = $_REQUEST("echostr");
            $postStr = file_get_contents('php://input');
            if (empty($postStr)) {
                echo $echostr;  // 没有任何POST参数的时候，这是一个服务器验证信息
                exit();
            } else {
                $xml = \Module\Weixin::Instance()->decryption($postStr, $msgSignature, $timestamp, $nonce);
                $this->data = \Util\Xml::xml2Array($xml);
                $msgType = $eventType = '';
                if (!empty($this->data['Event'])) {
                    $msgType = 'event';
                    $eventType = $event = strtolower($this->data['Event']);
                } elseif (!empty($this->data['MsgId'])) {
                    $eventType = $msgType = 'message';
                }

                // 消息去重检查
                $repeatKey = "{$this->data['FromUserName']}_{$this->data['CreateTime']}";
                if (!\Module\Weixin::Instance()->checkRepeat($repeatKey, $eventType)) {
                    // 消息判断
                    switch ($msgType) {
                        // 事件推送判断
                        case 'event':
                            switch ($event) {
                                case 'subscribe':       // 订阅
                                    $this->subscribeEvent();
                                    break;
                                case 'unsubscribe':     // 取消订阅
                                    $this->unSubscribeEvent();
                                    break;
                                case 'scan':        // 用户已关注时的二维码扫描
                                    $this->scanEvent();
                                    break;
                                case 'location':    // 上报地理位置事件
                                    $this->onLocation();
                                    break;
                                case 'click':       // 点击菜单拉取消息时的事件
                                    $this->onClick();
                                    break;
                                case 'view':        // 点击菜单跳转链接时的事件
                                    $this->onView();
                                    break;
                                case "scancode_push":   // 点击菜单扫码扫码推事件
                                case "scancode_waitmsg":    // 点击菜单扫码推事件且弹出“消息接收中”提示框
                                case "pic_sysphoto":    // 点击菜单弹出系统拍照发图
                                case "pic_photo_or_album":  // 点击菜单弹出拍照或者相册发图
                                case "pic_weixin":      // 点击菜单弹出微信相册发图器
                                case "location_select":     // 点击菜单弹出地理位置选择器
                                    // todo
                                    break;
                                // 模板内容发送结束
                                case 'templatesendjobfinish':
                                    $this->onTemplateSendJobSendFinish();
                                    break;
                                // 其他事件
                                default:
                                    break;
                            }
                            break;

                        case 'message':     // 普通消息
                            $this->onMessage();
                            break;
                        default:    // 其他消息处理
                            break;
                    }
                }

                echo "success";
                exit();
            }
        }
    }

    /**
     * 检查签名.
     * 
     * @param string $signature 微信加密签名.
     * @param string $timestamp 时间戳.
     * @param string $nonce     随机数.
     * 
     * @return boolean
     */
    private function checkSignature($signature, $timestamp, $nonce)
    {
        $token = \Config\Common::$weixinInfo['token'];
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            return true;
        } else {
            $this->log("{$signature}|{$timestamp}|{$nonce}", 'signatureFailed');
            return false;
        }
    }

    /**
     * 关注事件处理.
     *
     * @return void
     */
    private function subscribeEvent()
    {
        $openid = $this->data['FromUserName'];

        // 带场景值的处理
        if (!empty($this->data['EventKey'])) {
            $senceId = str_replace('qrscene_', '', $this->data['EventKey']);
            if (isset(\Config\Common::$subscribeOrScanSenceResponse[$senceId])) {
                $this->newsMessageResponse($openid, $this->data['ToUserName'], \Config\Common::$subscribeOrScanSenceResponse[$senceId]);
            }
        }

        // 默认的回复
        $this->textMessageResponse($openid, $this->data['ToUserName'], "感谢关注本公众号，在这儿你会发现新大陆。\n回复笑话每天一笑，回复对象找男(女)朋友");
    }

    /**
     * 取消关注事件处理.
     *
     * @return void
     */
    private function unsubscribeEvent()
    {
        // todo
    }

    /**
     * 用户扫描二维码时触发.
     * 
     * @return void
     */
    private function scanEvent()
    {
        // 带场景值的处理（回复一个图文消息）
        if (!empty($this->data['EventKey']) && isset(\Config\Common::$subscribeOrScanSenceResponse[$this->data['EventKey']])) {
            $this->newsMessageResponse($this->data['FromUserName'], $this->data['ToUserName'], \Config\Common::$subscribeOrScanSenceResponse[$this->data['EventKey']]);
        }
    }

    /**
     * 用户地理位置信息上报时触发.
     * 
     * @return void
     */
    private function onLocation()
    {
        // todo
    }

    /**
     * 点击菜单拉取消息时的事件.
     *
     * @return void
     */
    private function onClick()
    {
        $userInfo = \Module\Api::Instance()->getSubscribeUserInfo($this->data['FromUserName']);
        $nickname = !empty($userInfo['nickname']) ? $userInfo['nickname'] : "我";
        switch ($this->data['EventKey']) {
            case "curiosity":
                $text = "九旬老太为何惨死街头 数百头母驴为何半夜惨叫 \n小卖部安全套为何屡遭黑手 女生宿舍内裤为何频频失窃\n连环强奸母猪案究竟是何人所为 老尼姑的门夜夜被敲究竟是人是鬼\n 数百头母狗意外身亡背后又隐藏着什么 这一切的背后!!\n是人性的扭曲还是道德的沦丧?是性的爆发还是饥渴的无奈 \n敬请关注今晚八点CCTV1频道[{$nickname}的不归之路] 让我们跟随镜头走进变态狂的内心世界.";
                break;
            case "luck":
                $n = rand(1, 10);
                if ($n == 2) {
                    $text = "今天要走桃花运";
                } elseif ($n == 8) {
                    $text = "有财运，去买注彩票吧";
                } elseif ($n == 4) {
                    $text = "今日不宜出门";
                } else {
                    $text = "今天运气貌似还不错哦";
                }
                break;
            case "girl_friend":
                $text = "想得美";
                break;
        }

        if ($text) {
            $this->textMessageResponse($this->data['FromUserName'], $this->data['ToUserName'], $text);
        }
    }

    /**
     * 当用户打开view链接时触发.
     * 
     * @return void
     */
    private function onView()
    {
        // todo
    }

    /**
     * 模板发送内容结束时.
     *
     * @return void
     */
    private function onTemplateSendJobSendFinish()
    {
        $this->log("{$this->data['FromUserName']}\t{$this->data['MsgID']}\t{$this->data['Status']}", "templateResult");
    }

    /**
     * 普通消息处理.
     * 
     * @return void
     */
    private function onMessage()
    {
        if ($this->data['MsgType'] == "text" && !empty($this->data['Content'])) {
            if ($this->data['Content'] == "笑话") {
                $j = date('j');
                $text = \Config\Common::$joke[$j];
            } elseif ($this->data['Content'] == "对象") {
                $text = "对象还不简单，new一个就是了";
            }

            if (!empty($text)) {
                $this->textMessageResponse($this->data['FromUserName'], $this->data['ToUserName'], $text);
            }
        }

        // 消息转发到客服
        $this->transferCustomerServiceResponse($this->data['FromUserName'], $this->data['ToUserName']);
    }

}
