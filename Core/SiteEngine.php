<?php
/**
 * 路由解析处理.
 */

/**
 * Class SiteEngine.
 */
class SiteEngine
{

    /**
     * 路由解析及运行.
     * 
     * @return void
     */
    public function run()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $uri = current(explode('?', $uri));
        $uri = current(explode('&', $uri));
        $uri = trim($uri, '/');

        $router = array_filter(explode('/', $uri));
        if (count($router) == 0) {
            $router = array('Index', 'Index');
        } elseif (count($router) == 1) {
            $router[] = "Index";
        }
        $router = array_map("ucfirst", $router);

        $action = "Action_" . end($router);
        array_pop($router);

        $controller = "Controller_" . implode('_', $router);
        \Core\ControllerAutoLoader::Load($controller);
        if (!class_exists($controller) || !method_exists($controller, $action)) {
            Header("Location: /Index/PageNotFind");
            exit();
        }

        $o = new $controller();
        $o->$action();
    }

}
