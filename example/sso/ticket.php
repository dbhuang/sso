<?php
/**
 * sso_ticket.php
 * @Author dbhuang
 * @Time   2020/5/21 10:48
 */
include '../../vendor/autoload.php';
session_start();
if($_GET['service']!=''){ //带ticket来验证的
    //取出service的参数进行数据验证
    $client = $_GET['service'];

    try {
        $dataValidation = new \dbhuang\sso\DataValidation('loginticketkey','loginticketsecret');
        $data = $dataValidation->checkSign($client);


        //验证数据完成，验证ticket有效性
        $ssoService = new \dbhuang\sso\Service();
        $userId = $ssoService->checkTicketInRedis(['host'=>'127.0.0.1','port'=>6379,'password' =>'secret','database' => 15],$data['ticket']);

        if($userId===false){
            echo json_encode(['code'=>1,'data'=>'','msg'=>'无效的ticket']);exit();
        }



        echo json_encode(['code'=>0,'data'=>['userId'=>$userId],'msg'=>'成功']);exit();

    }catch (Exception $e){
        echo json_encode(['code'=>1,'data'=>$e->getCode(),'msg'=>$e->getMessage()]);exit();
    }


}else{
    echo json_encode(['code'=>1,'data'=>'','msg'=>'非法请求']);exit();
}