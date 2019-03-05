<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        $akId = "";//此处填写你的AccessKey ID
        $akSecret = "";//此处填写你的Access Key Secret

        $url = "https://dtplus-cn-shanghai.data.aliyuncs.com/face/attribute";//此处为请求的api地址请根据实际情况进行修改

        $content_arr['type'] = 0;
        $content_arr['image_url'] = '';
        $content = json_encode($content_arr);

        $options = array(
            'http' => array(
                'header' => array(
                    'accept'=> "application/json",
                    'content-type'=> "application/json",
                    'date'=> gmdate("D, d M Y H:i:s \G\M\T"),
                    'authorization' => ''
                ),
                'method' => "POST", //可以是 GET, POST, DELETE, PUT 此处修改为了POST
                'content' => $content, //如有数据，请用json_encode()进行编码
            )
        );
        $http = $options['http'];
        $header = $http['header'];
        $urlObj = parse_url($url);
        if(empty($urlObj["query"]))
            $path = $urlObj["path"];
        else
            $path = $urlObj["path"]."?".$urlObj["query"];
        $body = $http['content'];
        if(empty($body))
            $bodymd5 = $body;
        else
            $bodymd5 = base64_encode(md5($body,true));
        $stringToSign = $http['method']."\n".$header['accept']."\n".$bodymd5."\n".$header['content-type']."\n".$header['date']."\n".$path;
        $signature = base64_encode(
            hash_hmac(
                "sha1",
                $stringToSign,
                $akSecret, true));
        $authHeader = "Dataplus "."$akId".":"."$signature";
        $options['http']['header']['authorization'] = $authHeader;
        $options['http']['header'] = implode(
            array_map(
                function($key, $val){
                    return $key.":".$val."\r\n";
                },
                array_keys($options['http']['header']),
                $options['http']['header']));
        $context = stream_context_create($options);
        $file = file_get_contents($url, false, $context );

        $file = json_decode($file,true);
        dump($file);
    }
}
