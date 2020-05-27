<?php
/**
 * Ticket.php
 * @Author dbhuang
 * @Time   2020/5/19 18:06
 */

namespace dbhuang\sso;


use dbhuang\sso\exceptions\TicketException;
use Predis\Client;

class Service
{
    /**
     * 返回ticket同时记录到redis
     * @param $parameters
     * @param $userId
     * @return string
     * @throws TicketException
     * created by dbhuang at 2020/5/21 12:10
     */
    public function createTicketToRedis($parameters,$userId){

            //产生唯一字符串
            $ticket = md5(uniqid(microtime(true),true));

            if($userId<=0){
                throw new TicketException('利用redis存储ticket,请设置用户唯一标识',100202);
            }

        try {
            $redis = new Client($parameters);
            $redis->set($ticket,$userId,'EX',60);
            $result =$redis->exists($ticket);

            if($result){
                return $ticket;
            }else{
                throw new TicketException('',100201);
            }
        }catch (\Exception $e){
            throw new TicketException('利用redis存储ticket失败：'.$e->getMessage(),100200);
        }
    }

    /**
     * 返回ticket
     * @return string
     * created by dbhuang at 2020/5/21 12:10
     */
    public function createTicket(){
        return  md5(uniqid(microtime(true),true));
    }

    /**
     * 验证ticket是否在缓存中
     * @param $parameters
     * @param $ticket
     * @return bool|string
     * @throws TicketException
     * created by dbhuang at 2020/5/21 15:25
     */
    public function checkTicketInRedis($parameters,$ticket){
        try {
            $redis = new Client($parameters);
            if($userId = $redis->get($ticket)){
                return $userId;
            }else{
                return false;
            }
        }catch (\Exception $e){
            throw new TicketException($e->getMessage(),100400);
        }
    }

    /**
     * 生成sso服务端请求客户端的签名所需key,secret
     * @param $clients
     * @param $client
     * @return mixed
     * created by dbhuang at 2020/5/22 12:29
     */
    public function collectClient($clients,$client){
        $clientHost = url_host($client);
        if(!in_array($clientHost,$clients)){
            $clients[] = $clientHost;
        }
        return $clients;
    }

}