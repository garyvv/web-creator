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