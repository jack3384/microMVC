<?php
/**
 * 仅支持Linux环境
 * by:JACK
 */

namespace jikai\Components;


class SysInfo
{

    /**
     * @param $path
     * @return array|bool 单位为字节
     */
    public function getDisk($path)
    {
        if (file_exists($path)) {
            $rs = array();
            $rs['free'] = disk_free_space($path);
            $rs['total'] = disk_total_space($path);
            return $rs;
        } else {
            return false;
        }
    }

    /**
     * @return array
     * 该方法仅支持linux
     */
    public function getCpu()
    {
        $data = file('/proc/stat');
        if (!$data) {
            return false;
        }
        $data = $data[0];
        $parts = explode(' ', $data);
        $rs = array();

        $rs['user'] = (int)$parts[2];
        $rs['userNice'] = (int)$parts[3];
        $rs['system'] = (int)$parts[4];
        $rs['idle'] = (int)$parts[5];
        $rs['iowa'] = (int)$parts[6];
        $rs['total'] = (int)array_sum($parts);
        return $rs;
    }

    /**
     * @return array|bool 单位为kb.
     */
    public function getMem()
    {
        $data = file_get_contents('/proc/meminfo');
        if (!$data) {
            return false;
        }
        $rs = array();
        preg_match('/MemTotal:\s+(\d+) kB/', $data, $matches);
        $rs['total'] = (int)$matches[1];

        preg_match('/MemFree:\s+(\d+) kB/', $data, $matches);
        $rs['free'] = (int)$matches[1];

        return $rs;
    }

}