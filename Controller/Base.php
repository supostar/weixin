<?php
/**
 * 基础类.
 */

/**
 * Controller_Base.
 */
class Controller_Base
{

    /**
     * 写日志.
     *
     * @param string $message  内容.
     * @param string $category 类别.
     *
     * @return void
     */
    public function log($message, $category = '')
    {
        \Module\Base::Instance()->log($message, $category);
    }

    /**
     * 输出xml格式数据.
     *
     * @param string|array $msg 消息内容.
     *
     * @return string
     */
    public function response($msg)
    {
        $xml = \Util\Xml::array2Xml(array('xml' => $msg), true, 'utf-8', true);
        return \Module\Weixin::Instance()->encryption($xml);
    }

    /**
     * 文本格式的消息返回.
     * 
     * @param string $toUserName   发送对象(用户的openid).
     * @param string $fromUserName 发送者.
     * @param string $text         文案.
     *
     * @return mixed
     */
    public function textMessageResponse($toUserName, $fromUserName, $text)
    {
        $msg = array(
            'ToUserName' => "<![CDATA[{$toUserName}]]>",
            'FromUserName' => "<![CDATA[{$fromUserName}]]>",
            'CreateTime' => time(),
            'MsgType' => 'text',
            'Content' => "<![CDATA[{$text}]]>",
        );
        echo $this->reponse($msg);
        exit();
    }

    /**
     * 图片格式的消息返回.
     *
     * @param string $toUserName   发送对象(用户的openid).
     * @param string $fromUserName 发送者.
     * @param string $mediaId      素材多媒体id.
     *
     * @return mixed
     */
    public function imageMessageResponse($toUserName, $fromUserName, $mediaId)
    {
        $msg = array(
            'ToUserName' => "<![CDATA[{$toUserName}]]>",
            'FromUserName' => "<![CDATA[{$fromUserName}]]>",
            'CreateTime' => time(),
            'MsgType' => 'image',
            'MediaId' => "<![CDATA[{$mediaId}]]>",
        );
        echo $this->reponse($msg);
        exit();
    }

    /**
     * 语音格式的消息返回.
     *
     * @param string $toUserName   发送对象(用户的openid).
     * @param string $fromUserName 发送者.
     * @param string $mediaId      素材多媒体id.
     *
     * @return mixed
     */
    public function voiceMessageResponse($toUserName, $fromUserName, $mediaId)
    {
        $msg = array(
            'ToUserName' => "<![CDATA[{$toUserName}]]>",
            'FromUserName' => "<![CDATA[{$fromUserName}]]>",
            'CreateTime' => time(),
            'MsgType' => 'voice',
            'MediaId' => "<![CDATA[{$mediaId}]]>",
        );
        echo $this->reponse($msg);
        exit();
    }

    /**
     * 视频格式的消息返回.
     *
     * @param string $toUserName   发送对象(用户的openid).
     * @param string $fromUserName 发送者.
     * @param string $mediaId      素材多媒体id.
     * @param string $title        标题.
     * @param string $desc         描述.
     *
     * @return mixed
     */
    public function videoMessageResponse($toUserName, $fromUserName, $mediaId, $title = "", $desc = "")
    {
        $msg = array(
            'ToUserName' => "<![CDATA[{$toUserName}]]>",
            'FromUserName' => "<![CDATA[{$fromUserName}]]>",
            'CreateTime' => time(),
            'MsgType' => 'video',
            'MediaId' => "<![CDATA[{$mediaId}]]>",
            'Title' => "<![CDATA[{$title}]]>",
            'Description' => "<![CDATA[{$desc}]]>",
        );
        echo $this->reponse($msg);
        exit();
    }

    /**
     * 音乐格式的消息返回.
     *
     * @param string $toUserName   发送对象(用户的openid).
     * @param string $fromUserName 发送者.
     * @param string $thumbMediaId 缩略图的媒体id，通过素材管理中的接口上传多媒体文件，得到的id.
     * @param string $title        标题.
     * @param string $desc         描述.
     * @param string $musicUrl     音乐链接.
     * @param string $hQMusicUrl   高质量音乐链接，WIFI环境优先使用该链接播放音乐.
     *
     * @return mixed
     */
    public function musicMessageResponse($toUserName, $fromUserName, $thumbMediaId, $title = "", $desc = "", $musicUrl = "", $hQMusicUrl = "")
    {
        $msg = array(
            'ToUserName' => "<![CDATA[{$toUserName}]]>",
            'FromUserName' => "<![CDATA[{$fromUserName}]]>",
            'CreateTime' => time(),
            'MsgType' => 'music',
            'Title' => "<![CDATA[{$title}]]>",
            'Description' => "<![CDATA[{$desc}]]>",
            'MusicURL' => "<![CDATA[{$musicUrl}]]>",
            'HQMusicUrl' => "<![CDATA[{$hQMusicUrl}]]>",
            'ThumbMediaId' => "<![CDATA[{$thumbMediaId}]]>",
        );
        echo $this->reponse($msg);
        exit();
    }

    /**
     * 图文格式的消息返回.
     *
     * @param string $toUserName   发送对象(用户的openid).
     * @param string $fromUserName 发送者.
     * @param array  $items        图文内容.
     *
     * @return mixed
     */
    public function newsMessageResponse($toUserName, $fromUserName, $items)
    {
        if (!empty($items)) {
            $msg = array(
                'ToUserName' => "<![CDATA[{$toUserName}]]>",
                'FromUserName' => "<![CDATA[{$fromUserName}]]>",
                'CreateTime' => time(),
                'MsgType' => "news",
                'ArticleCount' => count($items)
            );
            $articles = '';
            foreach ($items as $item) {
                $itemTpl = " <item>
                                            <Title><![CDATA[%s]]></Title> 
                                            <Description><![CDATA[%s]]></Description> 
                                            <PicUrl><![CDATA[%s]]></PicUrl>
                                            <Url><![CDATA[%s]]></Url>
                                        </item>";
                $articles .= sprintf($itemTpl, $item['title'], $item['desc'], $item['pic_url'], $item['url']);
            }
            $msg['Articles'] = $articles;
        }

        echo $this->response($msg);
        exit();
    }

    /**
     * 转发消息到客服.
     *
     * @param string $toUserName   发送对象(用户的openid).
     * @param string $fromUserName 发送者.
     *
     * @return mixed
     */
    public function transferCustomerServiceResponse($toUserName, $fromUserName)
    {
        $data = array(
            'ToUserName' => "<![CDATA[{$toUserName}]]>",
            'FromUserName' => "<![CDATA[{$fromUserName}]]>",
            'CreateTime' => time(),
            'MsgType' => '<![CDATA[transfer_customer_service]]>',
        );
        echo $this->response($data);
        exit();
    }

}
