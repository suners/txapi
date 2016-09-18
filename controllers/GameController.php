<?php

namespace txapi\controllers;

!defined('ROOT') AND exit('Access Denied!');

include ROOT . '/controllers/BaseController.php';
include ROOT . '/lib/Weixin.php';

use txapi\lib\Weixin;
use txapi\model\DB;

/**
 * GameController
 * @author abin <rawuzebin@126.com>
 */
class GameController extends \txapi\controllers\BaseController
{

    public function getPicList()
    {
        //获取图片配置
        $picList = include ROOT.'/config/pictureConfig.php';
        $this->response(200, $picList);
    }

    public function initGame()
    {
        $wxCode    = isset($this->_params['code']) ? trim($this->_params['code']) : '';
        $picId     = isset($this->_params['picId']) ? trim($this->_params['picId']) : '';
        $time      = time();
        //获取图片配置
        $picList = include ROOT.'/config/pictureConfig.php';

        if(empty($wxCode) || empty($picId) || !isset($picList[$picId])){
            $this->response(400, '缺少参数');
        }
        //获取微信用户信息
        $user = $this->getWxUserInfo($wxCode);
        $uid  = $user['uid'];

        //先查找是否已经存在此游戏信息了
        $gSql = "SELECT * FROM tx_pic_game WHERE uid = {$uid} AND pic_id = {$picId}";
        $pic = DB::model()->query($gSql, 'Row');

        //若不存在则创建
        if(empty($pic)){

            $gData = array(
                'uid'            => $uid,
                'pic_id'         => $picId,
                'collected_list' => '',
                'is_all'         => 0,
                'created_at'     => $time,
                'expired_at'     => $time + $picList['expires']
            );

            $gRes = DB::model()->insert('tx_pic_game', $gData);

            if($gRes){
                $gid = DB::model()->lastInsertId();
            } else {
                $this->response(400, '获取游戏信息失败');
            }

            $resData = array(
                'gid'           => $gid,
                'uid'           => $uid,
                'collectedList' => '',
                'isAll'         => 0,
                'expiredAt'     => $gData['expired_at'],
                'friendList'    => array(),
                'isEnd'         => 0,
                'isCollected'   => 0,
            );

        } else {

            $fSql = "SELECT * FROM tx_collected_user WHERE gid = {$pic['gid']} AND is_valid = 1";
            $collectedUser = DB::model()->query($fSql, 'All');
            $friendList = array();

            $isCollected = 0;

            foreach($collectedUser as $cu){
                $sql = "SELECT * FROM tx_wxuser WHERE uid = {$cu['uid']}";
                $userInfo = DB::model()->query($sql, 'Row');
                //判断用户是否收集过
                if($cu['uid'] == $uid){
                    $isCollected = 1;
                }

                $friendList[] = array(
                    'nickname'   => $userInfo['nickname'],
                    'headimgurl' => $userInfo['headimgurl'],
                );
            }

            $resData = array(
                'gid'           => $pic['gid'],
                'uid'           => $uid,
                'collectedList' => $pic['collected_list'],
                'isAll'         => $pic['is_all'],
                'expiredAt'     => $pic['expired_at'],
                'friendList'    => $friendList,
                'isEnd'         => ($pic['expired_at'] < $time) ? 1 : 0,
                'isCollected'   => $isCollected,
            );
        }

        $this->response(200, $resData);
    }

    public function collect()
    {
        $gid = isset($this->_params['gid']) ? intval($this->_params['gid']) : '';
        $uid = isset($this->_params['uid']) ? intval($this->_params['uid']) : '';
        $time = time();

        if(empty($gid) || empty($uid)){
            $this->response(400, '缺少参数');
        }

        $gSql = "SELECT * FROM tx_pic_game WHERE gid = {$gid}";
        $pic = DB::model()->query($gSql, 'Row');

        if(empty($pic)){
            $this->response(400, '游戏不存在');
        }

        //判断游戏是否结束
        if($pic['expired_at'] < $time || $pic['is_all'] == 1){
            $this->response(400, '游戏已经结束');
        }

        //已收集的碎片列表
        $collectedList = !empty($pic['collected_list']) ? explode(',', $pic['collected_list']) : array();

        //判断用户是否已经收集过此游戏了
        $fSql = "SELECT * FROM tx_collected_user WHERE gid = {$pic['gid']} AND uid = {$uid}";
        $isCollected = DB::model()->query($fSql, 'Row');

        if($isCollected){
            $this->response(400, '你已经参与过了');
        }

        //随机生成一个碎片ID
        $chipId = rand(1, 9);
        $cData = array(
            'gid'        => $gid,
            'uid'        => $uid,
            'chip_id'    => $chipId,
            'created_at' => $time,
        );
        //判断此碎片是否已经存在
        if(in_array($chipId, $collectedList)){
            $cData['is_valid'] = 0;
        } else {
            $cData['is_valid'] = 1;
            $collectedList[] = $chipId;
            $updateData = array(
                'collected_list' => implode(',', $collectedList),
            );
            //如果收集了九张碎片，就完成拼图了
            if(count($collectedList) == 9){
                $updateData['is_all'] = 1;
                $updateData['finished_at'] = $time;
            }
            //更新游戏信息
            $gures = DB::model()->update('tx_pic_game', $updateData, "gid = {$gid}");
            if(!$gures){
                $this->response(400, '收集失败');
            }
        }

        //保存收集信息
        $cres = DB::model()->insert('tx_collected_user', $cData);
        if($cres){
            $this->response(200, array('chipId' => $chipId));
        } else {
            $this->response(400, '收集失败');
        }
    }


    private function getWxUserInfo($wxCode)
    {
        $appID     = 'wxa4fcf604e13ca421';
        $appSecret = '92a754615638cb886471ab0a02b807cc';
        $time      = time();
        $weixinObj = new Weixin($appID, $appSecret);

        //获取授权信息
        $authInfo = $weixinObj->getAuthorization($wxCode);
        if(!$authInfo){
            $this->response(400, '获取游戏信息失败');
        }

        //先查找是否存在此用户
        $uSql = "SELECT * FROM tx_wxuser WHERE openid = '{$authInfo['openid']}'";
        $user = DB::model()->query($uSql, 'Row');

        //若用户不存在
        if(empty($user)){

            //获取微信用户信息
            $wxUser = $weixinObj->getInfo($authInfo['access_token'], $authInfo['openid']);
            if(!$wxUser){
                $this->response(400, '获取游戏信息失败');
            }

            $user = array(
                'openid'     => $wxUser['openid'],
                'nickname'   => $wxUser['nickname'],
                'headimgurl' => $wxUser['headimgurl'],
                'unionid'    => isset($wxUser['unionid']) ? $wxUser['unionid'] : '',
                'created_at' => $time,
            );
            $res = DB::model()->insert('tx_wxuser', $user);
            if($res){
                $uid = DB::model()->lastInsertId();
            } else {
                $this->response(400, '获取游戏信息失败');
            }

            $user['uid'] = $uid;

        }

        return $user;
    }

}
