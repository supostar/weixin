<?php
/**
 * 菜单相关.
 */

/**
 * Controller_Menu.
 */
class Controller_Menu extends Controller_Base
{

    /**
     * 菜单创建.
     *
     * @return void
     */
    public function Action_Create()
    {
        $result = \Module\Api::Instance()->createMenus();
        if ($result) {
            echo 'Menus created successfully!';
        } else {
            echo 'Menus creating failed!';
        }
    }

}
