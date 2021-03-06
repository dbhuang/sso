<?php
/**
 * index.php
 * @Author dbhuang
 * @Time   2020/5/20 15:58
 */
include '../../vendor/autoload.php';
session_start();

if($_SESSION['APP_USER']){
    print_r($_SESSION['APP_USER']);
    echo '已经登录了app1<a href="/logout.php">退出</a>';
    exit();
}

if($_GET['ticket']){    //有ticket
    //把ticket解密取出token提交到sso验证
    try {
        $ssoUrl = 'http://sso.host.com/ticket.php';
        $ssoClient = new \dbhuang\sso\Client();
        $result = $ssoClient->ticketToService($ssoUrl);
        if($result['code']==0){
            //登录成功,存储用户登录状态
            echo '登录成功';
            $_SESSION['APP_USER'] = $result;
            header('Location:/');
        }else{
            echo $result['code'].$result['msg'];exit();
        }
    }catch (Exception $e){
        //登录失败,对应跳转
        echo $e->getCode().$e->getMessage();exit();
    }
    exit();
}else{  //没有ticket
    $ssoUrl = 'http://sso.host.com/login.php';
    $ssoClient = new \dbhuang\sso\Client();
    $ssoClient->redirectService($ssoUrl);
}