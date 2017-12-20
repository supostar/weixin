<?php
/**
 * Api接口相关.
 */

/**
 * Controller_Api.
 */
class Controller_Api extends Controller_Base
{

    /**
     * 上传图片至微信服务器.
     * 
     * @return void
     */
    public function Action_Uploadimg()
    {
        $url = \JMGetRequest("url");
        if (empty($url)) {
            die(json_encode(array('errcode' => 1, 'errmsg' => 'url is null')));
        }

        for ($i = 0; $i < 3; $i++) {
            $returnContent = $this->httpGetData($url);
            if ($returnContent !== false) {
                break;
            }
        }
        if ($returnContent === false) {
            die(json_encode(array('errcode' => 1, 'errmsg' => 'download pic failed')));
        }

        $path = "/tmp/weixin";
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $ext = pathinfo($url, PATHINFO_EXTENSION);   // 获取文件后缀
        $ext = in_array($ext, array('jpg', 'png')) ? $ext : 'jpg';
        $newName = date('YmdHis') . rand(10000, 99999) . '.' . $ext;    // 文件重命名
        $file = $path . "/" . $newName;
        $fp = @fopen($file, "a");       // 将文件绑定到流
        fwrite($fp, $returnContent);         // 写入文件

        $res = \Module\Api::Instance()->uploadimg($file);
        @unlink($file);     // 删除临时文件

        if (!empty($res['url'])) {
            die(json_encode(array('errcode' => 0, 'errmsg' => '', 'url' => $res['url'])));
        }

        exit(json_encode(array('errcode' => 2, 'msg' => $res['errcode'] . ':' . $res['errmsg'])));     // 微信接口调用失败
    }

}
