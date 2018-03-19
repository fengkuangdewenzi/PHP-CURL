<?php
//抓歌手列表
include('init.php');

$url='http://www.1ting.com/group/group0_2.html';
$result=$curl->read($url);
$html=phpQuery::newDocumentHTML($result['content']);
$li=$html['ul.allSinger li a'];
$st=$db->prepare("insert into artist(name,url) values(?,?)");
foreach($li as $v){
    $v=pq($v);
    $st->execute(array(trim($v->text()),trim($v->attr('href'))));
}