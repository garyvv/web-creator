<?php
/**
 * Created by PhpStorm.
 * User: gary
 * Date: 2017/11/17
 * Time: 22:47
 */

namespace Garyvv\WebCreator;


interface Creator
{
    /**
     * 处理来源图片的函数
     * @param $dir
     * @param $dirServer
     * @param string $htmlName
     * @return mixed
     */
    public function dealImage($dir, $dirServer, $htmlName = 'article');
}