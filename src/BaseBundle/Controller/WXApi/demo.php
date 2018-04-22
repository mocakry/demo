<?php

	require_once __DIR__ . "/WxApi.php";
    //获取用户openid
    $tools = new WxApi();
    $to_url = 'http://'.$_SERVER['HTTP_HOST'] . '/weixin.php';
    $openId = $tools->GetOpenid($to_url);
    echo $openId;