<?php
/**
 * User: Jenner
 * Date: 2015/5/15
 * Time: 15:40
 * modify:jack
 * 基于http://www.huyanping.cn/php%E6%96%87%E4%BB%B6%E7%BC%93%E5%AD%98%E5%AE%9E%E7%8E%B0/ 上文档修改
 */

namespace jikai\Components;

class FileCache
{

    /**
     * 功能实现：get、set、has、increment、decrement、delete、flush(删除所有)
     * DOC：new FileCache(缓存根目录名)
     * get($key) 获取值，set($key,$val,[$过期时间，不设置表示不过期]),increment($key,num)表示值+1或者自定义
     *
     *
     *为了避免一个文件内的数据过大，造成读取文件的时候延迟较高，我们采用一个key-value一个文件的方式实现存储结构。
     *为了支持key过期，我们需要把expire数据写入到文件中，所以需要对写入的数据进行序列化处理
     *为了能够快速的定位到文件路径，我们采用hash算法一次计算出文件位置
     * 避免过多缓存文件放在同一文件夹下，所以 参看path()函数
     * 根据md5($key)实现3级目录存放文件 例如： /home/b0/68/b068931cc450442b63f5b3d276ea4297
     * 缓存目录
     * @var
     */
    private $cache_dir;

    /**
     * @param $cache_dir
     * @throws \Exception
     */
    public function __construct($cache_dir = null)
    {
        $this->cache_dir = empty($cache_dir) ? __DIR__ : $cache_dir;
        if (!is_dir($this->cache_dir)) {
            $make_dir_result = mkdir($cache_dir, 0755, true);
            if ($make_dir_result === false) {
                throw new \Exception('Cannot create the cache directory');
            }
        }
    }

    /**
     * 根据key获取值，会判断是否过期
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        $cache_data = $this->getItem($key);
        if ($cache_data === false || !is_array($cache_data)) {
            return false;
        }

        return $cache_data['data'];
    }

    /**
     * 添加或覆盖一个key
     * @param $key
     * @param $value
     * @param $expire
     * @return mixed
     */
    public function set($key, $value, $expire = 0)
    {
        return $this->setItem($key, $value, time(), $expire);
    }

    /**
     * 设置包含元数据的信息
     * @param $key
     * @param $value
     * @param $time
     * @param $expire
     * @return bool
     */
    private function setItem($key, $value, $time, $expire)
    {
        $cache_file = $this->createCacheFile($key);
        if ($cache_file === false) {
            return false;
        }

        $cache_data = array('data' => $value, 'time' => $time, 'expire' => $expire);
        $cache_data = json_encode($cache_data);

        $put_result = file_put_contents($cache_file, $cache_data);
        if ($put_result === false) {
            return false;
        }

        return true;
    }

    /**
     * 创建缓存文件
     * @param $key
     * @return bool|string
     */
    private function createCacheFile($key)
    {
        $cache_file = $this->path($key);
        if (!file_exists($cache_file)) {
            $directory = dirname($cache_file);
            if (!is_dir($directory)) {
                $make_dir_result = mkdir($directory, 0755, true);
                if ($make_dir_result === false) {
                    return false;
                }
            }
            $create_result = touch($cache_file);
            if ($create_result === false) {
                return false;
            }
        }

        return $cache_file;
    }

    /**
     * 判断Key是否存在
     * @param $key
     * @return mixed
     */
    public function has($key)
    {
        $value = $this->get($key);
        if ($value === false) {
            return false;
        }

        return true;
    }

    /**
     * 加法递增
     * @param $key
     * @param int $value
     * @return mixed
     */
    public function increment($key, $value = 1)
    {
        $item = $this->getItem($key);
        if ($item === false) {
            $set_result = $this->set($key, $value);
            if ($set_result === false) {
                return false;
            }
            return $value;
        }

        $check_expire = $this->checkExpire($item);
        if ($check_expire === false) {
            return false;
        }

        $item['data'] += $value;

        $result = $this->setItem($key, $item['data'], $item['time'], $item['expire']);
        if ($result === false) {
            return false;
        }

        return $item['data'];
    }

    /**
     * 减法递增
     * @param $key
     * @param int $value
     * @return mixed
     */
    public function decrement($key, $value = 1)
    {
        $item = $this->getItem($key);
        if ($item === false) {
            $value = 0 - $value;
            $set_result = $this->set($key, $value);
            if ($set_result === false) {
                return false;
            }
            return $value;
        }

        $check_expire = $this->checkExpire($item);
        if ($check_expire === false) {
            return false;
        }

        $item['data'] -= $value;

        $result = $this->setItem($key, $item['data'], $item['time'], $item['expire']);
        if ($result === false) {
            return false;
        }

        return $item['data'];
    }

    /**
     * 删除一个key，同会删除缓存文件
     * @param $key
     * @return mixed
     */
    public function delete($key)
    {
        $cache_file = $this->path($key);
        if (file_exists($cache_file)) {
            $unlink_result = unlink($cache_file);
            if ($unlink_result === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * 清楚所有缓存
     * @return mixed
     */
    public function flush()
    {
        return $this->delTree($this->cache_dir);
    }

    /**
     * 递归删除目录
     * @param $dir
     * @return bool
     */
    function delTree($dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    /**
     * 根据key获取缓存文件路径
     *
     * @param  string $key
     * @return string
     */
    protected function path($key)
    {
        $parts = array_slice(str_split($hash = md5($key), 2), 0, 2);
        return $this->cache_dir . '/' . implode('/', $parts) . '/' . $hash;
    }

    /**
     * 获取含有元数据的信息
     * @param $key
     * @return bool|mixed|string
     */
    protected function getItem($key)
    {
        $cache_file = $this->path($key);
        if (!file_exists($cache_file) || !is_readable($cache_file)) {
            return false;
        }

        $cache_data = file_get_contents($cache_file);
        if (empty($cache_data)) {
            return false;
        }
        $cache_data = json_decode($cache_data, true);
        if ($cache_data) {
            $check_expire = $this->checkExpire($cache_data);
            if ($check_expire === false) {
                $this->delete($key);
                return false;
            }
        }

        return $cache_data;
    }

    /**
     * 检查key是否过期
     * @param $cache_data
     * @return bool
     */
    protected function checkExpire($cache_data)
    {
        $time = time();
        $is_expire = intval($cache_data['expire']) !== 0 && (intval($cache_data['time']) + intval($cache_data['expire']) < $time);
        if ($is_expire) {
            return false;
        }

        return true;
    }
}