<?php
//下载歌手图片
include('init.php');

$dir=dirname(__FILE__).'/pic';
$baseUrl='http://www.1ting.com';
$artistList=$db->query("select id,name,url from artist")->fetchAll();
foreach($artistList as $v){
	$url=array($baseUrl.$v['url']);
	$callback=array('demo5_cb1',array($v['id'],$v['name']));
	$curl->add($url,$callback);
}
$curl->go();

//处理歌手详情页的回调函数
function demo5_cb1($r,$id,$name){
	global $db,$curl,$dir;
	if($r['info']['http_code']==200){
		$html=phpQuery::newDocumentHTML($r['content']);
		$list=$html['dl.singerInfo dt img'];
		if(!empty($list)){
			foreach($list as $v){
				$v=pq($v);
				$picUrl=$v->attr('src');
				$ext=pathinfo($picUrl,PATHINFO_EXTENSION);
				if(!empty($name) && !empty($ext)){
					$filename=$name.'.'.$ext;
					$file=$dir.'/'.$filename;
					$url=array($picUrl,$file);
					$callback=array('demo5_cb2',array($id,$filename));
					$curl->add($url,$callback);
				}
			}
		}
	}
	//$curl->status();
}

//图片下载完成回调函数
function demo5_cb2($r,$id,$filename){
	global $db;
	if($r['info']['http_code']==200){
		if($db->exec("update artist set pic='$filename' where id=$id")){
			echo $r['info']['url']."\n";
		}
	}	
}