<?php
/**
 * DataValidation.php
 * @Author dbhuang
 * @Time   2020/5/19 18:09
 */

namespace dbhuang\sso;


use dbhuang\sso\exceptions\DataException;

class DataValidation
{
    private $key = '';              //签名所需key
    private $secret = '';           //签名所需secret
    private $expirationTime = 60;   //超时时间

    /**
     * 设置应用唯一参数
     * @param $key
     * @param $secret
     * @param int $expirationTime
     * created by dbhuang at 2020/5/21 10:37
     */
    public function __construct($key,$secret,$expirationTime=60){
        $this->key = $key;
        $this->secret = $secret;
        $this->expirationTime = $expirationTime;
    }

    /**
     * 返回query参数
     * @param $url
     * @return mixed
     * created by dbhuang at 2020/5/21 15:09
     */
    public function parseQuery($url){
        $service = parse_url($url);
        parse_str($service['query'],$query);
        return $query;
    }

    /**
     * 对数据进行签名
     * @param $url
     * @param $param
     * @return string
     * created by dbhuang at 2020/5/21 15:08
     */
    public function makeSignUrl($url,$param){
        try{
            $url = build_url($url,$param);

            $data = $this->parseQuery($url);

            if(!isset($data['expirationTime'])){
                $data['expirationTime'] = time();
            }
            $data['key'] = $this->key;
            ksort($data);       //根据键对数组进行升序排序
            $hashData ='';
            foreach($data as $k=>$v){
                $hashData .= rawurlencode($v);
            }
            $data['sign'] = hash_hmac('md5',$hashData, $this->secret );
            return build_url($url,$data);
        }catch (\Exception $e){
            throw new DataException('生成签名地址失败：'.$e->getMessage(),100300);
        }
    }

    /**
     * 验证数据签名
     * @param $url
     * @return mixed
     * @throws DataException
     * created by dbhuang at 2020/5/21 15:12
     */
    public function checkSign($url){
        $data = $this->parseQuery($url);

        if($data['key'] != $this->key){
            throw new DataException('应用key不合法',100100);
        }

        if(time()-$data['expirationTime']>$this->expirationTime){
            throw new DataException('请求数据超过有效期',100101);
        }
        if(empty($data['sign'])){
            throw new DataException('缺少签名参数',100102);
        }
        $paramSign =$data['sign'];
        unset($data['sign']);

        ksort($data);       //根据键对数组进行升序排序
        $hashData ='';
        foreach($data as $k=>$v){
            $hashData .= rawurlencode($v);
        }

        if($paramSign != hash_hmac('md5',$hashData, $this->secret )){
            throw new DataException('请求数据验签失败',100103);
        }

        return $data;
    }
}