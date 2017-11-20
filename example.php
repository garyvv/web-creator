<?php
/**
 * Created by PhpStorm.
 * User: gary
 * Date: 2017/11/17
 * Time: 21:58
 * /data/htdocs/packagist/storage/images
 */

require_once __DIR__ . '/vendor/autoload.php';

use Garyvv\WebCreator\WeChatCreator;
use Garyvv\WebCreator\TmallCreator;

if (isset($_POST['content'])) {
//    $web = new WeChatCreator($_POST['content']);
    $web = new TmallCreator($_POST['content']);
    $web->dealImage('/data/htdocs/packagist/storage/html', 'http://packagist.local.com/storage/html/');
    var_dump($web->link);
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>富文本框HTML生成器</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        ul, ol {
            list-style: none;
        }

        body {
            font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
        }
    </style>
</head>
<body text=#000000 bgColor="#ffffff">
<header>
    <h1>富文本框HTML生成器</h1>
</header>

<form id="edit-content" action="" method="POST" style="padding: 3%;">
    <label for="redactor_content">内容</label>
    <textarea id="redactor_content" name="content" style="height: 560px;"></textarea>
    <hr>
    <button style="width: 70px;height: 35px; margin-top: 10px;background-color: #fff" type="submit">提交修改</button>
    <br>
    <br>
</form>

</body>

<link rel="stylesheet" href="http://simditor.tower.im/assets/styles/simditor.css">

<script type="text/javascript" src="http://simditor.tower.im/assets/scripts/jquery.min.js"></script>
<script type="text/javascript" src="http://simditor.tower.im/assets/scripts/mobilecheck.js"></script>
<script type="text/javascript" src="http://simditor.tower.im/assets/scripts/module.js"></script>
<script type="text/javascript" src="http://simditor.tower.im/assets/scripts/hotkeys.js"></script>
<script type="text/javascript" src="http://simditor.tower.im/assets/scripts/uploader.js"></script>
<script type="text/javascript" src="http://simditor.tower.im/assets/scripts/simditor.js"></script>
<script>
    var editor = new Simditor({
        textarea: $('#redactor_content')
    });
</script>
</html>