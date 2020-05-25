<?php
/**
 * Client.php
 * @Author dbhuang
 * @Time   2020/5/20 9:31
 */

namespace dbhuang\sso;


use dbhuang\sso\exceptions\LogoutException;
use dbhuang\sso\exceptions\TicketException;

class Client
{
    /**
     * 跳转到sso登录服务器
     * @param $loginUrl
     * @return string
     * created by dbhuang at 2020/5/20 9:57
     */
    public function redirectService($loginUrl){
        $locationUrl = build_url($loginUrl,['service'=>current_url()]);
        header("HTTP/1.1 302 Found");
        header('Location: '.$locationUrl);
    }

    /**
     * 把客户端有的ticket请求到服务器验证
     * @param $validateUrl
     * @return mixed
     * @throws TicketException
     * created by dbhuang at 2020/5/21 10:16
     */
    public function ticketToService($validateUrl){
        try {
            $validateUrl = build_url($validateUrl,['service'=>current_url()]);
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET',$validateUrl);
            $code = $response->getStatusCode();
            $result = $response->getBody()->getContents();
            if($code==200){
                //响应内容有问题
                $result = json_decode($result,true);
                if($result===null || $result===false){
                    throw new TicketException('解析响应内容失败',100002);
                }

                //响应结果
                if($result['code']==0){
                    return $result['data'];
                }else{
                    throw new TicketException($result['msg'],$result['data']);
                }
            }else{
                throw new TicketException('请求失败，返回状态码 :'.$code,100001);
            }
        }catch (\Exception $e){
            throw new TicketException($e->getMessage(),$e->getCode()?:100000);
        }
    }

    /**
     * 发起退出所有应用申请
     * @param $logoururl
     * @throws LogoutException
     * created by dbhuang at 2020/5/22 11:41
     */
    public function logoutAll($logoururl){
        try {
            $validateUrl = build_url($logoururl,['service'=>current_url()]);
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET',$validateUrl);
            $code = $response->getStatusCode();
            $result = $response->getBody()->getContents();

        }catch (\Exception $e){
            throw new LogoutException($e->getMessage(),$e->getCode()?:100500);
        }
    }
}