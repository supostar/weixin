<?php
/**
 * 自动加载相关.
 */

namespace Core;

/**
 * 自动加载类.
 */
class Autoloader
{

    protected static $sysRoot = array();
    protected static $instance;
    protected $classPrefixes = array();

    /**
     * 构造函数.
     */
    protected function __construct()
    {
        static::$sysRoot = array();
    }

    /**
     * 获得实例.
     *
     * @return self
     */
    public static function instance()
    {
        if (!static::$instance) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    /**
     * 添加根目录. 默将使用Autoloader目录所在的上级目录为根目录.
     *
     * @param string $path 目录.
     *
     * @return self
     */
    public function addRoot($path)
    {
        static::$sysRoot[] = $path;
        return $this;
    }

    /**
     * 按命名空间自动加载相应的类.
     *
     * @param string $name 命名空间及类名.
     *
     * @return boolean
     */
    public function loadByNamespace($name)
    {
        $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $name);

        foreach (static::$sysRoot as $root) {
            $classFile = $root . $classPath . '.php';
            if (is_file($classFile)) {
                require_once($classFile);
                if (class_exists($name, false)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * 注册自动加载方法.
     * 
     * @return self
     */
    public function init()
    {
        spl_autoload_register(array($this, 'loadByNamespace'));
        return $this;
    }

}

/**
 * 控制器的自动加载.
 */
class ControllerAutoLoader
{

    /**
     * 控制器的自动加载处理.
     *
     * @param string $name 控制器的类名.
     *
     * @return boolean
     */
    public static function Load($name)
    {
        $a = explode('_', $name);
        $fn = ROOT_PATH . implode('/', $a) . '.php';

        if (!file_exists($fn)) {
            return false;
        }
        require_once $fn;

        return true;
    }

}

spl_autoload_register(array('\Core\ControllerAutoLoader', 'Load'));
