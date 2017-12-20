<?php
/**
 * 默认页.
 */

/**
 * Controller_Index.
 */
class Controller_Index extends Controller_Base
{

    /**
     * 首页.
     *
     * @return void
     */
    public function Action_Index()
    {
        die("微信公众号开发");
    }

    /**
     * 页面未找到.
     *
     * @return void
     */
    public function Action_PageNotFind()
    {
        die("404");
    }

}
