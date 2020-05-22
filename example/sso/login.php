<?php
/**
 * sso.php
 * @Author dbhuang
 * @Time   2020/5/20 15:58
 */
include '../../vendor/autoload.php';
session_start();

if($_SESSION["SSO_NAME"]){ //已经登录直接颁发ticket

    try {
        //获得ticket并存储到redis
        $ssoService = new \dbhuang\sso\Service();
        $ticket = $ssoService->createTicketToRedis(['host'=>'127.0.0.1','port'=>6379,'password' =>'secret','database' => 15],$_SESSION["SSO_USRE_ID"]);

        //对带有ticket的数据进行签名
        $serviceUrl = $_GET['service'];
        $dataValidation = new \dbhuang\sso\DataValidation('loginticketkey','loginticketsecret');
        $signUrl = $dataValidation->makeSignUrl($serviceUrl,['ticket'=>$ticket]);


        //把所有的子应用给记录起来
        $clients = $_SESSION['SSO_CLIENTS']??[];
        $_SESSION['SSO_CLIENTS'] = $ssoService->collectClient($clients,$serviceUrl);

        echo '<script type="text/javascript">window.onload=function(){window.location.href = "'.$signUrl.'";}</script>';
        exit();
    }catch (Exception $e){
        echo '<script type="text/javascript">window.onload=function(){alert(".$e->getMessage().");}</script>';
        exit();
    }
}

if($_GET['service']!='' && count($_POST)==0){ //没登录进入登录界面
    setcookie('service',$_GET['service']);
    include 'login.html';
    exit();
}

if($_POST['username'] && $_POST['password']){ //登录表单提交
    $user = ['zhangsan'=>1,'lisi'=>2];
    if(isset($user[$_POST['username']]) && $_POST['password']=='123456'){
        //登录成功，存储用户登录状态
        $_SESSION["SSO_NAME"] = $_POST['username'];
        $_SESSION["SSO_USRE_ID"] = $user[$_POST['username']];

        try {
            //获得ticket并存储到redis
            $ssoService = new \dbhuang\sso\Service();
            $ticket = $ssoService->createTicketToRedis(['host'=>'127.0.0.1','port'=>6379,'password' =>'secret','database' => 15],$_SESSION["SSO_USRE_ID"]);

            //对带有ticket的数据进行签名
            $serviceUrl = $_COOKIE['service'];
            $dataValidation = new \dbhuang\sso\DataValidation('loginticketkey','loginticketsecret');
            $signUrl = $dataValidation->makeSignUrl($serviceUrl,['ticket'=>$ticket]);

            //把所有的子应用给记录起来
            $clients = $_SESSION['SSO_CLIENTS']??[];
            $_SESSION['SSO_CLIENTS'] = $ssoService->collectClient($clients,$serviceUrl);

            echo json_encode(['code'=>0,'data'=>['signUrl'=>$signUrl],'msg'=>'成功']);exit();
        }catch (Exception $e){
            echo json_encode(['code'=>1,'data'=>'','msg'=>$e->getMessage()]);exit();
        }
    }else{
        echo json_encode(['code'=>1,'data'=>'','msg'=>'失败']);exit();
    }
}
