<?php
namespace txapi\lib;
/**
 * 微信SDK 用来获取授权信息 和 用户信息
 * 
 * @author abin <rawuzebin@126.com>
 */
class Weixin 
{
    public $appID = '';
    public $appSecret = '';
    public $authorizationUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token?'; //授权地址
    public $refreshUrl = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?'; //刷新授权地址
    public $userinfoUrl = 'https://api.weixin.qq.com/sns/userinfo?'; //获取用户信息url
    public $codeUrl = 'https://open.weixin.qq.com/connect/qrconnect?'; //获取code地址 (pc)
    public $codeUrlClient = 'https://open.weixin.qq.com/connect/oauth2/authorize?'; //获取code地址 (微信客户端)
    
    public function __construct($appID, $appSecret)
    {
        $this->appID = $appID;
        $this->appSecret = $appSecret;
    }
    
    /**
     * 获取授权信息
     * 
     * 正确返回：
     * { 
     *   "access_token":"ACCESS_TOKEN", 
     *   "expires_in":7200, 
     *   "refresh_token":"REFRESH_TOKEN",
     *   "openid":"OPENID", 
     *   "scope":"SCOPE",
     *   "unionid": "o6_bmasdasdsad6_2sgVt7hMZOPfL"  //只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。
     * }
     * 
     * @param type $code
     */
    public function getAuthorization($code)
    {
        if(empty($code) || empty($this->appID) || empty($this->appSecret)){
            return false;
        }
        
        $url = $this->authorizationUrl."appid={$this->appID}&secret={$this->appSecret}&code={$code}&grant_type=authorization_code";
        
        $authorizationInfo = $this->curl($url);
        
        if(empty($authorizationInfo) || isset($authorizationInfo['errcode'])){
            return false;
        } else {
            return $authorizationInfo;
        }
        
    }
    
    /**
     * 刷新授权码
     * 
     * 正确返回：
     * { 
     *   "access_token":"ACCESS_TOKEN", 
     *   "expires_in":7200, 
     *   "refresh_token":"REFRESH_TOKEN", 
     *   "openid":"OPENID", 
     *   "scope":"SCOPE" 
     * }
     * 
     * @param type $refreshToken
     * @return mix
     */
    public function refreshToken($refreshToken)
    {
        if(empty($refreshToken) || empty($this->appID) || empty($this->appSecret)){
            return false;
        }
        
        $url = $this->refreshUrl."appid={$this->appID}&grant_type=refresh_token&refresh_token={$refreshToken}";
        
        $res = $this->curl($url);
        
        if(empty($res) || isset($res['errcode'])){
            return false;
        } else {
            return $res;
        }
    }
    
    /**
     * 获取用户信息
     * 
     * { 
     *   "openid":"OPENID",
     *   "nickname":"NICKNAME",
     *   "sex":1,
     *   "province":"PROVINCE",
     *   "city":"CITY",
     *   "country":"COUNTRY",
     *   "headimgurl": "http://wx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0",
     *   "privilege":[
     *   "PRIVILEGE1", 
     *   "PRIVILEGE2"
     *   ],
     *   "unionid": " o6_bmasdasdsad6_2sgVt7hMZOPfL"
     * }
     * 
     * @param type $accessToken
     * @param type $openID
     * @return mix
     */
    public function getInfo($accessToken, $openID)
    {
        if(empty($accessToken) || empty($openID)){
            return false;
        }
        
        $url = $this->userinfoUrl."access_token={$accessToken}&openid={$openID}";
        
        $userInfo = $this->curl($url);
        
        if(empty($userInfo) || isset($userInfo['errcode'])){
            return false;
        } else {
            return $userInfo;
        }
    }
    
    /**
     * 生成codeurl
     * 
     * @param type $redirectUrl
     * @param type $state
     * @param type $scope
     * @param type $isPc
     * @return type
     */
    public function getCodeURL($redirectUrl,$isPc = true, $state = 266, $scope = 'snsapi_login')
    {
        $params = array(
            'appid'         => $this->appID,
            'redirect_uri'  => $redirectUrl,
            'response_type' => 'code',
            'scope'         => $scope,
            'state'         => $state
        );
        $url = $isPc ? $this->codeUrl : $this->codeUrlClient;
        return $url.http_build_query($params).'#wechat_redirect';
    }
    
    /**
     * curl 发送数据请求
     *
     * @param string  $url
     * @param array   $postData
     * @param array   $header
     * @param int     $timeout
     */
    private function curl($url, $timeout = 20)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书  
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 检查证书中是否设置域名  
        
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result,true);
    }
}
