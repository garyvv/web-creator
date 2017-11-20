<?php

namespace Garyvv\WebCreator;

use DiDom\Document;
use GuzzleHttp\Client;

class TmallCreator extends HtmlController implements Creator
{

    public function __construct($html, $options = [])
    {
        parent::__construct($html, $options);

    }

    public function dealImage($dir, $dirServer, $htmlName = 'article')
    {
        $html = new Document($this->html);

        $client = new Client(['verify' => false]);  //忽略SSL错误

        $this->html = htmlspecialchars_decode($this->html);

        foreach ($html->find('img') as $item) {
            $src = $item->src;
            if (strpos($src, $dirServer) !== false) {
//                本域图片不需处理
                continue;
            }

            $tmp = explode('/', $src);
//            图片名称，使用原名称
            $fileName = array_pop($tmp);
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

        $this->html = $this->header . $this->html . $this->footer;

        $htmlFile = $dir . '/' . $htmlName . '.html';
        file_put_contents($htmlFile, $this->html);

        $this->link = $dirServer . $htmlName . '.html';

    }

}