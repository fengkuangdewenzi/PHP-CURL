<?php
//使用缓存抓取专辑列表
include('init.php');

$baseUrl='http://www.1ting.com';
$artistList=$db->query("select id,url from artist")->fetchAll();
$curl->cache['on']=true;
$curl->cache['dir']=dirname(__FILE__).'/cache';
foreach($artistList as $v){
    $url=array($baseUrl.$v['url']);
    $callback=array('demo7_cb1',array($v['id']));
    $curl->add($url,$callback);
}
$curl->go();

//处理歌手详情页的回调函数
function demo7_cb1($r,$id){
    global $db,$curl;
    if($r['info']['http_code']==200){
        $html=phpQuery::newDocumentHTML($r['content']);
        $list=$html['div.albumList ul li a.albumLink'];
        if(!empty($list)){
            $st=$db->prepare('insert into album(artist_id,name,url) values(?,?,?)');
            foreach($list as $v){
                $v=pq($v);
                $st->execute(array($id,trim($v->find('span.albumName')->text()),trim($v->attr('href'))));
            } 
        }
    }
    $curl->status();
}