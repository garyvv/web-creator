<?php

namespace Garyvv\WebCreator;


class HtmlController extends Oss
{
    public $html;
    public $images;
    public $link;

    public $header;
    public $footer;
    public $style = "<style>img {width: 100%;}</style>";

    public function __construct($html, $options = [])
    {
        $title = '';
        isset($options['title']) && $title = "<title>" . $options['title'] . "</title>";

        $description = '';
        isset($options['description']) && $description = "<meta name=\"description\" content=\"" . $options['description'] . "\" />";

        $keywords = '';
        isset($options['keywords']) && $keywords = "<meta name=\"keywords\" content=\"" . $options['keywords'] . "\" />";


        $this->html = $html;

        $this->header = '<!DOCTYPE html><html>
                <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0">
                <meta name="apple-mobile-web-app-capable" content="yes">
                <meta name="apple-mobile-web-app-status-bar-style" content="black">
                <meta name="format-detection" content="telephone=no">
                ' . $title . $description . $keywords . $this->style . '
                </head>';
        $this->footer = '</html>';

    }

    public function setHeader($header)
    {
        $this->header = $header;
    }

    public function setFooter($footer)
    {
        $this->footer = $footer;
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

            $imageUrl = 'http://' . $this->aliOssViewDomain . '/' . $imageObject;

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