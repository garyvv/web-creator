<?php
/**
 * Created by PhpStorm.
 * User: gary
 * Date: 2017/11/17
 * Time: 22:53
 */

namespace Garyvv\WebCreator;

use OSS\OssClient;

class Oss
{
    protected $aliOssBucket;
    protected $aliOssAppId;
    protected $aliOssAppSecret;
    protected $aliOssEndpoint;
    protected $aliOssViewDomain;

    protected $ossPrefix = '';


    /**
     * @param $config
     * @throws \Exception
     */
    public function setOss($config)
    {
        if (!isset($config['bucket'])) throw new \Exception('bucket is required');
        if (!isset($config['app_id'])) throw new \Exception('app_id is required');
        if (!isset($config['app_secret'])) throw new \Exception('app_secret is required');
        if (!isset($config['end_point'])) throw new \Exception('end_point is required');
        if (!isset($config['view_domain'])) $config['view_domain'] = 'http://' . $this->aliOssBucket . '.' . $this->aliOssEndpoint;

        $this->aliOssBucket = $config['bucket'];
        $this->aliOssAppId = $config['app_id'];
        $this->aliOssAppSecret = $config['app_secret'];
        $this->aliOssEndpoint = $config['end_point'];
        $this->aliOssViewDomain = $config['view_domain'];

        if (isset($config['bucket_prefix'])) $this->ossPrefix = $config['bucket_prefix'];
    }

    public function uploadFileToOSS($object, $file)
    {
        if (!is_file($file)) return false;

        $oss = new OssClient($this->aliOssAppId, $this->aliOssAppSecret, $this->aliOssEndpoint);
        return $oss->uploadFile($this->aliOssBucket, $object, $file);
    }

    public function uploadObjectToOSS($object, $json)
    {
        if (empty($json)) return false;

        $oss = new OssClient($this->aliOssAppId, $this->aliOssAppSecret, $this->aliOssEndpoint);
        return $oss->putObject($this->aliOssBucket, $object, $json);
    }

    public function isObjectExist($object)
    {
        $oss = new OssClient($this->aliOssAppId, $this->aliOssAppSecret, $this->aliOssEndpoint);
        return $oss->doesObjectExist($this->aliOssBucket, $object);
    }
}