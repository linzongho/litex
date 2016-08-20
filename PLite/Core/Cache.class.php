<?php
namespace PLite\Core;
use PLite\Lite;
use PLite\PLiteException;
use PLite\Util\SEK;
use PLite\Utils;

/**
 * Interface CacheInterface 缓存驱动接口
 * @package Kbylin\System\Library\Cache
 */
interface CacheInterface {
    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $replacement
     * @return mixed
     */
    public function get($name,$replacement=null);

    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     * @param int $expire  有效时间，0为永久（以秒计时）
     * @return boolean
     */
    public function set($name, $value, $expire = 0);

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    public function delete($name);

    /**
     * 清除缓存
     * @access public
     * @return boolean
     */
    public function clean();
}

/**
 * Class Cache
 *
 * @method string get(string $name,$replace=null) static 读取缓存
 * @method boolean set(string $name,mixed $value,int $expire) static 写入缓存
 * @method int delete(string $name) static 删除缓存
 * @method int clean() static empty the cache
 * @package PLite\Core
 */
class Cache extends Lite{

    const CONF_NAME = 'cache';
    const CONF_CONVENTION = [
        PRIOR_INDEX => 0,
        DRIVER_CLASS_LIST => [
            'PLite\\Core\\Cache\\File',
            'PLite\\Core\\Cache\\Memcache',
        ],
        DRIVER_CONFIG_LIST => [
            [
                //from thinkphp ,match case
                'expire'        => 0,
                'cache_subdir'  => false,
                'path_level'    => 1,
                'prefix'        => '',
                'length'        => 0,
                'path'          => PATH_RUNTIME.'/file_cache/',
                'data_compress' => false,
            ],
            [
                'host'      => 'localhost',
                'port'      => 11211,
                'expire'    => 0,
                'prefix'    => '',
                'timeout'   => 1000, // 超时时间（单位：毫秒）
                'persistent'=> true,
                'length'    => 0,
            ],
        ],
        'expire'    => 300,
    ];
    /** 
     * 键为ob缓存的level
     * @var array
     */
    private static $_idStack = [];

    /**
     * 判断是否存在指定的缓存
     * @param mixed $identify
     * @return bool
     */
    public static function has($identify){
        return null !== self::get(self::_buildIdentify($identify),null);
    }

    /**
     * 开始缓存直到调用save保存缓存
     * @param mixed $identify
     * @return void
     */
    public static function begin($identify){
        $identify = self::_buildIdentify($identify);
        ob_start() or PLiteException::throwing('Cache begin failed!');
        $level = ob_get_level();
        self::$_idStack[$level] = $identify;
    }

    /**
     * @param int|null $expire
     * @return void
     */
    public static function save($expire=null){
        $level = ob_get_level();
        if(isset($level)){
            $content = ob_get_contents();
            $content = ob_get_clean();
            return $content;
        }else{
            return false;
        }
    }

    /**
     * build identify
     * @param mixed $identify
     * @return string
     */
    private static function _buildIdentify($identify=null){
        if(!$identify){
            $identify = SEK::createGUID();
        }elseif(!is_string($identify)){
            $identify = md5(http_build_query($identify));
        }
        return $identify;
    }

}