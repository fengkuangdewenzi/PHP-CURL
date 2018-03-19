<?php
//从歌手列表页抓取所有歌曲列表
include('init.php');

$baseUrl='http://www.1ting.com';
$artistList=$db->query("select id,url from artist")->fetchAll();
foreach($artistList as $v){
	$url=array($baseUrl.$v['url']);
	$callback=array('demo3_cb1',array($v['id']));
	$curl->add($url,$callback);
}
$curl->go();

//处理歌手详情页的回调函数
function demo3_cb1($r,$id){
	global $db,$curl,$baseUrl;
	if($r['info']['http_code']==200){
		$html=phpQuery::newDocumentHTML($r['content']);
		$list=$html['div.albumList ul li a.albumLink'];
		if(!empty($list)){
			foreach($list as $v){
				$v=pq($v);
				$url=array($baseUrl.trim($v->attr('href')));
				//继续传递歌手id
				$callback=array('demo3_cb2',array($id,$url[0]));
				$curl->add($url,$callback);
			} 
		}
	}
	$curl->status(0);
}

//处理专辑详情页的回调函数
function demo3_cb2($r,$id,$url){
	global $db,$curl;
	if($r['info']['http_code']==200){
		$html=phpQuery::newDocumentHTML($r['content']);
		$list=$html['#song-list tr td.songTitle a.songName'];
		if(!empty($list)){
			$st=$db->prepare('insert into songlist(artist_id,name,album_url) values(?,?,?)');
			foreach($list as $v){
				$v=pq($v);
				$st->execute(array($id,trim($v->text()),$url));
			}  
		}
	}
}