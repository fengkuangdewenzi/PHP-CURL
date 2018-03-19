<?php
//大量任务的处理，抓专辑列表
include('init.php');

$baseUrl='http://www.1ting.com';
$curl->task='demo4_addTask';
$curl->go();

//取还没有添加的任务
function demo4_addTask(){
	global $baseUrl,$db,$curl;
	static $lastId=0;
	$limit=100;
	$list=$db->query("select id,url from artist where id>$lastId order by id limit $limit")->fetchAll();
	foreach($list as $v){
		$url=array($baseUrl.$v['url']);
		$callback=array('demo4_cb1',array($v['id']));
		$curl->add($url,$callback);
	}
	$lastId=$v['id'];
}

//处理歌手详情页的回调函数
function demo4_cb1($r,$id){
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