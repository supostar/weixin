<?php
/**
 * Redis操作类.
 */

namespace Module;

/**
 * Class \Module\Redis.
 */
class Redis
{

    private $redis;
    // 当前数据库ID号
    protected $dbId = 0;
    // 当前权限认证码
    protected $auth;
    private static $_instance = array();
    private $k;
    // 连接属性数组
    protected $attr = array(
        // 连接超时时间，redis配置文件中默认为300秒
        'timeout' => 30,
    );
    // 什么时候重新建立连接
    protected $expireTime;
    protected $host;
    protected $port;

    /**
     * 构造方法.
     *
     * @param array $config 配置.
     */
    private function __construct($config)
    {
        $this->redis = new \Redis();
        $this->port = $config['port'] ? $config['port'] : 6379;
        $this->host = $config['host'];
        $this->redis->connect($this->host, $this->port, $this->attr['timeout']);

        if ($config['auth']) {
            $this->auth($config['auth']);
            $this->auth = $config['auth'];
        }

        $this->expireTime = time() + $this->attr['timeout'];
    }

    /**
     * 得到实例化的对象(为每个数据库建立一个连接，如果连接超时，将会重新建立一个连接).
     *
     * @param string $endpoint Redis节点.
     *
     * @return \Module\Redis
     */
    public static function Instance($endpoint = "default")
    {
        $redisConfig = (array) new \Config\Redis();
        $config = isset($redisConfig[$endpoint]) ? $redisConfig[$endpoint] : $redisConfig['default'];

        $k = md5(implode('', $config));
        if (!isset(static::$_instance[$k]) || !(static::$_instance[$k] instanceof self)) {
            static::$_instance[$k] = new self($config);
            static::$_instance[$k]->k = $k;
            static::$_instance[$k]->dbId = $config['db'];
            static::$_instance[$k]->select($config['db']);
        } elseif (time() > static::$_instance[$k]->expireTime) {
            static::$_instance[$k]->close();
            static::$_instance[$k] = new self($config);
            static::$_instance[$k]->k = $k;
            static::$_instance[$k]->dbId = $config['db'];
            static::$_instance[$k]->select($config['db']);
        }

        return static::$_instance[$k];
    }

    /**
     * 禁止克隆.
     *
     * @return void
     */
    private function __clone()
    {
        
    }

    /**
     * 执行原生的redis操作.
     *
     * @return \Redis
     */
    public function getRedis()
    {
        return $this->redis;
    }

    /**
     * 关闭所有连接.
     *
     * @return void
     */
    public static function closeAll()
    {
        foreach (static::$_instance as $o) {
            if ($o instanceof self)
                $o->close();
        }
    }

    /**
     * 得到当前数据库ID.
     *
     * @return integer
     */
    public function getDbId()
    {
        return $this->dbId;
    }

    /**
     * 返回当前密码.
     *
     * @return string
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * 获取当前HOST.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * 获取当前端口.
     *
     * @return integer
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * 获取当前连接信息.
     *
     * @return array
     */
    public function getConnInfo()
    {
        return array(
            'host' => $this->host,
            'port' => $this->port,
            'auth' => $this->auth
        );
    }

}
