<?php
/**
 * logout.php
 * @Author dbhuang
 * @Time   2020/5/22 11:15
 */
include '../../vendor/autoload.php';
session_start();

if($_SESSION['APP_USER']){//当前用户自己操作退出
    if($_GET['sso']=='logout'){
        //退出登录
        $_SESSION['APP_USER'] = null;
        echo 'logout-app2';exit();
    }else{
        $ssoUrl = 'http://sso.host.com/logout.php';
        echo '<script type="text/javascript">window.onload=function(){window.location.href = "'.$ssoUrl.'";}</script>';exit();
    }

}else{
    echo '非法操作';
    exit();
}
