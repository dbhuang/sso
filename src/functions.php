<?php
namespace dbhuang\sso;

/**
 * 获得当前完整的请求地址
 * @return string
 * created by dbhuang at 2020/5/20 9:47
 */
function current_url(){
    $url = '';
    if((!empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https') || (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (! empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ) {
        $url .= 'https://';
    } else {
        $url .= 'http://';
    }

    $url .= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    return $url;
}


/**
 * 向请求地址追加参数
 * @param $url
 * @param array $query
 * @return string
 * created by dbhuang at 2020/5/20 16:40
 */
function build_url($url, $query=[]) {
    $url = urldecode($url);
    $parseUrl = parse_url($url);

    $new_url = $parseUrl['scheme'] . '://' . $parseUrl['host'];

    if(!empty($parseUrl['port'])){
        $new_url .= ':'.$parseUrl['port'];
    }

    $new_url .=  $parseUrl['path'];

    if(!empty($parseUrl['query'])) {
        parse_str($parseUrl['query'], $parseUrlQuery);
        $query = array_merge($parseUrlQuery, $query);
    }
    $query = http_build_query($query);
    $new_url .= $query!=''? '?'. $query:'';

    if(!empty($parseUrl['fragment'])){
        $new_url .= '#' . $parseUrl['fragment'];
    }

    return $new_url;
}

/**
 * 获得地址的域名
 * @param $url
 * @return string
 * created by dbhuang at 2020/5/22 11:12
 */
function url_host($url){
    $parseUrl = parse_url($url);
    return $parseUrl['scheme'] . '://' . $parseUrl['host'];
}

