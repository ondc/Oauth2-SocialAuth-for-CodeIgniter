<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 /**
  * Oauth2 SocialAuth for CodeIgniter
  * 微信 Provider
  * 
  * @author     fugang <fugang.bj@qq.com>
  */

class OAuth2_Provider_Wechat extends OAuth2_Provider
{
	public $name = 'wechat';

	public $human = '微信';

	public $uid_key = 'openid';
	
	public $client_id_key = 'appid';
  
	public $client_secret_key = 'secret';
	
	protected $scope = 'snsapi_login';//snsapi_userinfo

	public $method = 'POST';
	
	/**
	 * web二维码扫描[url_authorize description]
	 * @return [type] [description]
	 */
 	public function url_authorize()
	{
		return 'https://open.weixin.qq.com/connect/qrconnect';
	}

	public function url_access_token()
	{
		return 'https://api.weixin.qq.com/sns/oauth2/access_token';
	}

	public function get_user_info(OAuth2_Token_Access $token)
	{
		$url = 'https://api.weixin.qq.com/sns/userinfo?'.http_build_query(array(
			'access_token' => $token->access_token,
			'openid' => $token->uid,
			'lang' => 'zh_CN'
		));

		$user = json_decode(file_get_contents($url));

		if (array_key_exists("error", $user))
		{
		  	throw new OAuth2_Exception((array) $user);
		}
		log_message('debug','wechat_oauth'.json_encode($user));
		// Create a response from the request
		return array(
			'via' => 'wechat',
			'uid' => $user->unionid,
			'screen_name' => $user->nickname,
			'name' => $user->nickname,
			'location' => $user->province.$user->city,
			'description' => '',
			'image' => $user->headimgurl,
			'access_token' => $token->access_token,
			'expire_at' => $token->expires,
			'refresh_token' => $token->refresh_token,
			'source' => $this->source
		);

	}

}
