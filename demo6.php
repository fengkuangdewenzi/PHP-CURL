<?php
//自定义任务，检测歌手页的404情况。
include('init.php');

$baseUrl='http://www.1ting.com';
$artistList=$db->query("select id,url from artist")->fetchAll();
$curl->opt=array(CURLOPT_NOBODY=>true);
foreach($artistList as $v){
    $url=array($baseUrl.$v['url']);
    $callback=array('demo6_cb1',array($url[0]));
    $curl->add($url,$callback);
}
$curl->go();

//处理歌手详情页的回调函数
function demo6_cb1($r,$url){
    echo $r['info']['http_code']."\t".$url."\n";
}