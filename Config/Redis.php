<?php
/**
 * File: Redis.php
 *
 * @author mingl
 */

namespace Config;

/**
 * Class Common.
 */
class Redis
{

    // 默认的redis配置
    public $default = array(
        'port' => '6379',
        'host' => '127.0.0.1',
        'db' => '0',
        'auth' => ''
    );

}
