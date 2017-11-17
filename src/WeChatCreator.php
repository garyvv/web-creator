<?php

namespace Garyvv\WebCreator;

use DiDom\Document;
use GuzzleHttp\Client;

class WeChatCreator extends Oss implements Creator
{
	public $html;
	public $images;
	public $link;

    public $header;
    public $footer;

    private $isOss = false;

	private $editFlag = 'EDIT-FLAG'; //标识是否已自动加入头部，防止编辑重复

	public function __construct($html)
	{
		$this->html = $html;

        $this->header = '<!DOCTYPE html><html><head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0">
                <meta name="apple-mobile-web-app-capable" content="yes">
                <meta name="apple-mobile-web-app-status-bar-style" content="black">
                <meta name="others" content="' . $this->editFlag . '">
                <meta name="format-detection" content="telephone=no">' . $this->htmlStyle() . '</head>';
        $this->footer = '</html>';

	}

    private function htmlStyle()
    {
        return '<style>
                    img {
                        max-width: 100% !important;
                    }
                </style>';
    }

	public function setHeader($header)
	{
		$this->htmlHeader = $header;
	}

	public function setFooter($footer)
	{
		$this->footer = $footer;
	}

    /**
     * @param $config
     * @throws \Exception
     */
	public function setOss($config)
	{
	    if (!isset($config['bucket']))      throw new \Exception('bucket is required');
	    if (!isset($config['app_id']))      throw new \Exception('app_id is required');
	    if (!isset($config['app_secret']))  throw new \Exception('app_secret is required');
	    if (!isset($config['end_point']))   throw new \Exception('end_point is required');
	    if (!isset($config['view_domain'])) $config['view_domain'] = 'http://' . $this->aliOssBucket . '.' . $this->aliOssEndpoint;

		$this->aliOssBucket 		= $config['bucket'];
        $this->aliOssAppId 			= $config['app_id'];
        $this->aliOssAppSecret 		= $config['app_secret'];
        $this->aliOssEndpoint 		= $config['end_point'];
        $this->aliOssViewDomain 	= $config['view_domain'];

        $this->isOss = true;

        if (isset($config['bucket_prefix'])) $this->ossPrefix = $config['bucket_prefix'];
	}

	public function dealImage($dir, $dirServer, $htmlName = 'article')
	{
		$html = new Document($this->html);

        $client = new Client(['verify' => false]);  //忽略SSL错误

        foreach ($html->find('img') as $item) {
            $src = $item->src;
            if (strpos($src, $dirServer) !== false) {
//                本域图片不需处理
                continue;
            }

//            微信公众号后缀
            $type = 'jpg';
            $tmp = explode('wx_fmt=', $src);
            if (isset($tmp[1])) {
                $tmp = explode('&', $tmp[1]);
                isset($tmp[0]) && $type = $tmp[0];
            }

//            图片名称
            $fileName = substr(md5($src), 8, 16) . '.' . $type;
            $file = $dir . '/' . $fileName;

            if (file_exists($file) === false && strpos($src, 'http') !== false) {
//                下载
                $response = $client->get($src, ['save_to' => $file]);   //保存远程url到文件
            }

            $imageUrl = $dirServer . $fileName;

            $this->images[] = [
                'file_name' => $fileName,
                'file_dir' => $file,
                'url' => $imageUrl,
            ];

            $this->html = str_replace($src, $imageUrl, $this->html);

        }

        if (strpos($this->html, $this->editFlag) === false) {
            $this->html = $this->header . $this->html . $this->footer;
        }

        $htmlFile = $dir . '/' . $htmlName . '.html';
        file_put_contents($htmlFile, $this->html);

        $this->link = $dirServer . $htmlName . '.html';

	}


	public function uploadImageToOss($deleteLocal = true)
    {
        foreach ($this->images as $key => $image) {
            $imageObject = $this->ossPrefix . $image['file_name'];
            if ($this->isObjectExist($imageObject) === false) {
//                上传图片到 OSS
                $this->uploadFileToOSS($imageObject, $image['file_dir']);
//                删除本地文件
                $deleteLocal === true && @unlink($image['file_dir']);
            }

            $imageUrl = $this->aliOssViewDomain . '/' . $imageObject;

            $this->html = str_replace($image['url'], $imageUrl, $this->html);
            $this->images[$key]['url'] = $imageUrl;
        }

    }


    public function uploadHtmlToOss($objectName)
    {
//        上传HTML到OSS
        $htmlObject = $this->ossPrefix . $objectName;

        $this->uploadObjectToOSS($htmlObject, $this->html);

        $this->link = 'http://' . $this->aliOssViewDomain . '/' . $htmlObject;
    }

}