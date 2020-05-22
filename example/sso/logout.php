<?php
/**
 * logout.php
 * @Author dbhuang
 * @Time   2020/5/22 10:45
 */
session_start();

if($_SESSION["SSO_NAME"]){
    //把当前sso账号退出登录
    $_SESSION["SSO_NAME"] = null;
    $_SESSION["SSO_USRE_ID"] = null;

    $clients = $_SESSION["SSO_CLIENTS"];

    $html = '';
    foreach ($clients as $client){
        $client = $client.'/logout.php?sso=logout';
        $html .='<iframe src=\"'.$client.'\"  style = \"display:none;visibility:hidden\" ></iframe>';
    }

    echo '<html><head></head><body>退出成功</body></html><script type="text/javascript">window.onload=function(){var body=document.getElementsByTagName("body");var div = document.createElement("div");div.innerHTML ="'.$html.'";document.body.appendChild(div);}</script>';

    exit();

}else{
    echo '非法操作';
    exit();
}