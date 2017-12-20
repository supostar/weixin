<?php
/**
 * 基础类.
 */

namespace Module;

/**
 * Class \Module\Base.
 */
class Base
{

    protected static $instances = array();

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
     * 写日志.
     *
     * @param string $message  内容.
     * @param string $category 类别.
     *
     * @return void
     */
    public function log($message, $category = '')
    {
        $path = "/home/logs/wx/" . $category;
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $filename = date('Ymd');
        $file = $path . "/{$filename}.log";
        $message = date('Y-m-d H:i:s') . "\t $message \n";
        error_log($message, 3, $file);
    }

}
