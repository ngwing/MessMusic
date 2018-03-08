<?php
namespace app\music\service;
use think\Model;

class Qmusic extends Model {

    
    //获取QQ音乐歌单信息列表
    public function getQSongList(){
        $post = input('post.');
        if(!isset($post['Body']['SongListId'])){
            return ['ResultCode'=>1,'ErrCode'=>'3001','ErrMsg'=>'QQ SongListId not exists'];
        }
        $music =  model('music/Qmusic','model');
        $url = 'https://c.y.qq.com/qzone/fcg-bin/fcg_ucc_getcdinfo_byids_cp.fcg?g_tk=5381&uin=0&format=json&inCharset=utf-8&outCharset=utf-8&notice=0&platform=h5&needNewCode=1&new_format=1&pic=500&disstid='.$post['Body']['SongListId'].'&type=1&json=1&utf8=1&onlysong=0&picmid=1&nosign=1&song_begin=0&_=1516976108112';
        $musicInfo = $music->curl_get($url);
        $musicInfo = str_replace('\\', '', $musicInfo);
        $musicInfo = json_decode($musicInfo,1);
        if(!isset($musicInfo['cdlist'][0]['songlist'])||sizeof($musicInfo['cdlist'][0]['songlist'])<=0){
            return ['ResultCode'=>1,'ErrCode'=>'3001','ErrMsg'=>'QQ SongListId not exists'];
        }
        foreach ($musicInfo['cdlist'][0]['songlist'] as $key => $value) {
            // 歌名 歌手
            $body[$key]['mid'] = $value['mid'];
            $body[$key]['id'] = $value['id'];
            $body[$key]['title'] = $value['title'];
            $body[$key]['author'] = $value['singer'][0]['name'];
            //歌曲URL
            $body[$key]['url'] = $music->getQSongResURL($value['mid']);
            //歌曲PIC
            $body[$key]['pic'] = $music->getQSongPic($value['mid']);
            //歌曲LRC
            $body[$key]['lrc'] = $music->getQSongLrc($value['mid']);
            //时间
            $body[$key]['time'] = $value['interval'];
        }
        return ['ResultCode'=>1,'ErrCode'=>'OK','Body'=>$body];
    }
    //获取QQ音乐歌词
    public function getQSongLyric(){
        $post = input('post.');
        if(!isset($post['Body']['SongId'])){
            return ['ResultCode'=>1,'ErrCode'=>'3002','ErrMsg'=>'QQ SongId not exists'];
        }
        $body =  model('music/Qmusic','model')->getQSongLrc($post['Body']['SongId']);
        return ['ResultCode'=>1,'ErrCode'=>'OK','Body'=>$body];
    }
    //获取QQ音乐图片
    public function getQSongPic(){
        $post = input('post.');
        if(!isset($post['Body']['SongId'])){
            return ['ResultCode'=>1,'ErrCode'=>'3002','ErrMsg'=>'QQ SongId not exists'];
        }
        $body =  model('music/Qmusic','model')->getQSongPic($post['Body']['SongId']);
        return ['ResultCode'=>1,'ErrCode'=>'OK','Body'=>$body];
    }
    //获取QQ音乐资源链接
    public function getQSongResURL(){
        $post = input('post.');
        if(!isset($post['Body']['SongId'])){
            return ['ResultCode'=>1,'ErrCode'=>'3002','ErrMsg'=>'QQ SongId not exists'];
        }
        $body =  model('music/Qmusic','model')->getQSongResURL($post['Body']['SongId']);
        return ['ResultCode'=>1,'ErrCode'=>'OK','Body'=>$body];
    }
    //获取QQ音乐歌单信息列表
    public function getQPlayerSongList(){
        $post = input('post.');
        if(!isset($post['Body']['SongListId'])){
            return ['ResultCode'=>1,'ErrCode'=>'3001','ErrMsg'=>'QQ SongListId not exists'];
        }
        $music =  model('music/Qmusic','model');
        $url = 'https://c.y.qq.com/qzone/fcg-bin/fcg_ucc_getcdinfo_byids_cp.fcg?g_tk=5381&uin=0&format=json&inCharset=utf-8&outCharset=utf-8&notice=0&platform=h5&needNewCode=1&new_format=1&pic=500&disstid='.$post['Body']['SongListId'].'&type=1&json=1&utf8=1&onlysong=0&picmid=1&nosign=1&song_begin=0&_=1516976108112';
        $musicInfo = $music->curl_get($url);
        $musicInfo = str_replace('\\', '', $musicInfo);
        $musicInfo = json_decode($musicInfo,1);
        if(!isset($musicInfo['cdlist'][0]['songlist'])||sizeof($musicInfo['cdlist'][0]['songlist'])<=0){
            return ['ResultCode'=>1,'ErrCode'=>'3003','ErrMsg'=>'QQ SongList not exists'];
        }
        foreach ($musicInfo['cdlist'][0]['songlist'] as $key => $value) {
            // 歌名 歌手
            $mid = $value['mid'];
            $body[$key]['title'] = $value['title'];
            $body[$key]['author'] = $value['singer'][0]['name'];
            //歌曲URL
            $body[$key]['url'] = $music->getQSongResURL($mid);
            //歌曲PIC
            $body[$key]['pic'] = $music->getQSongPic($mid);
            //歌曲LRC
            $body[$key]['lrc'] = $music->getQSongLrc($mid);
        }
        return ['ResultCode'=>1,'ErrCode'=>'OK','Body'=>$body];
    }
    //搜索QQ音乐
    public function getQSongSearch(){
        $post = input('post.');
        if(!isset($post['Body']['key'])){
            return ['ResultCode'=>1,'ErrCode'=>'3004','ErrMsg'=>'Search Key not exists'];
        }
        $music =  model('music/Qmusic','model');
        $url = 'https://c.y.qq.com/soso/fcgi-bin/client_search_cp?ct=24&qqmusic_ver=1298&new_json=1&remoteplace=txt.yqq.center&searchid=49376627710948669&t=0&aggr=1&cr=1&catZhida=1&lossless=0&flag_qc=0&p=1&n=10&w='.$post['Body']['key'].'&g_tk=5381&jsonpCallback=MusicJsonCallback4603211876683677&loginUin=0&hostUin=0&format=jsonp&inCharset=utf8&outCharset=utf-8&notice=0&platform=yqq&needNewCode=0';
        $musicInfo = $music->curl_get($url);
        //去除无用字符
        $musicInfo = str_replace('MusicJsonCallback4603211876683677(', '', $musicInfo);
        //去除最后一个括号
        $musicInfo = rtrim($musicInfo, ')'); 
        //格式化
        $musicInfo = json_decode($musicInfo,1);
        //判断是否存在 否则返回null
        if(!isset($musicInfo['data']['song']['list'])||sizeof($musicInfo['data']['song']['list'])<=0){
            return ['ResultCode'=>1,'ErrCode'=>'OK','Body'=>'null'];
        }
        foreach ($musicInfo['data']['song']['list'] as $key => $value) {
            $mid = $value['mid'];
            // 歌名 歌手
            $body[$key]['title'] = $value['name'];
            $body[$key]['author'] = $value['singer'][0]['name'];
            $body[$key]['time'] = $value['interval'];
            //歌曲URL
            $body[$key]['url'] = $music->getQSongResURL($mid);
            //歌曲PIC
            $body[$key]['pic'] = $music->getQSongPic($mid);
            //歌曲LRC
            $body[$key]['lrc'] = $music->getQSongLrc($mid);
        }
        return ['ResultCode'=>1,'ErrCode'=>'OK','Body'=>$body];
    }
    //QQ热歌榜
    public function getQHotSongList(){
        $url = 'https://c.y.qq.com/v8/fcg-bin/fcg_v8_toplist_cp.fcg?tpl=3&page=detail&topid=26&type=top&song_begin=0&song_num=100&g_tk=5381&jsonpCallback=MusicJsonCallbacktoplist&loginUin=0&hostUin='.rand(100000,99999999).'&format=jsonp&inCharset=utf8&outCharset=utf-8&notice=0&platform=yqq&needNewCode=0';
        $music =  model('music/Qmusic','model');
        $musicInfo = $music->curl_get($url);
        //去除无用字符
        $musicInfo = str_replace('MusicJsonCallbacktoplist(', '', $musicInfo);
        //去除最后一个括号
        $musicInfo = rtrim($musicInfo, ')'); 
        //格式化
        $musicInfo = json_decode($musicInfo,1);
        //判断是否存在 否则返回null
        if(!isset($musicInfo['songlist'])||sizeof($musicInfo['songlist'])<=0){
            return ['ResultCode'=>1,'ErrCode'=>'OK','Body'=>'null'];
        }
        foreach ($musicInfo['songlist'] as $key => $value) {
            $mid = $value['data']['songmid'];
            // 歌名 歌手 时间
            $body[$key]['title'] = $value['data']['songname'];
            $body[$key]['author'] = $value['data']['singer'][0]['name'];
            $body[$key]['time'] = $value['data']['interval'];
            //歌曲URL
            $body[$key]['url'] = $music->getQSongResURL($mid);
            //歌曲PIC
            $body[$key]['pic'] = $music->getQSongPic($mid);
            //歌曲LRC
            $body[$key]['lrc'] = $music->getQSongLrc($mid);
        }
        return ['ResultCode'=>1,'ErrCode'=>'OK','Body'=>$body];
    }
}