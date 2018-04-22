<?php

namespace BaseBundle\Controller;

use BaseBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class JssdkController extends BaseController
{
	private $appId = 'wx2147a07bed40520f';
	private $appSecret = '89fb1ccb6c68113e3da55b0523c56fa1';

	public function getSignPackage() {
		$jsapiTicket = $this->getJsApiTicket();

		// 注意 URL 一定要动态获取，不能 hardcode.
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

		$timestamp = time();
		$nonceStr = $this->createNonceStr();

		// 这里参数的顺序要按照 key 值 ASCII 码升序排序
		$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

		$signature = sha1($string);

		$signPackage = array(
			"appId"     => $this->appId,
			"nonceStr"  => $nonceStr,
			"timestamp" => $timestamp,
			"url"       => $url,
			"signature" => $signature,
			"rawString" => $string
		);
		return $signPackage;
	}

	private function createNonceStr($length = 16) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}

	private function getJsApiTicket() {
		$conn = $this->getDoctrine()->getConnection();
		$jsapi_ticket = $conn->fetchColumn("SELECT value FROM config WHERE name = 'jsapi_ticket'");

		// jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
		$data = json_decode(trim(substr($jsapi_ticket, 15)));
		if ($data->expire_time < time()) {
			$accessToken = $this->getAccessTokenTime();
			// 如果是企业号用以下 URL 获取 ticket
			// $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
			$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
			$res = json_decode($this->httpGet($url));
			$ticket = $res->ticket;
			if ($ticket) {
				$data->expire_time = time() + 7000;
				$data->jsapi_ticket = $ticket;
				$conn->update('config', array('value' => "<?php exit();?>".json_encode($data)), array('name' => 'jsapi_ticket'));
			}
		} else {
			$ticket = $data->jsapi_ticket;
		}

		return $ticket;
	}

	private function httpGet($url) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 500);
		// 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
		// 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_URL, $url);

		$res = curl_exec($curl);
		curl_close($curl);

		return $res;
	}

	private function getApiTicket() {
		$conn = $this->getDoctrine()->getConnection();
		$api_ticket = $conn->fetchColumn("SELECT value FROM config WHERE name = 'api_ticket'");

		// jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
		$data = json_decode(trim(substr($api_ticket, 15)));
		if ($data->expire_time < time()) {
			$accessToken = $this->getAccessTokenTime();
			// 如果是企业号用以下 URL 获取 ticket
			// $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
			$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=wx_card&access_token=$accessToken";
			$res = json_decode($this->httpGet($url));
			$ticket = $res->ticket;
			if ($ticket) {
				$data->expire_time = time() + 7000;
				$data->api_ticket = $ticket;
				$conn->update('config', array('value' => "<?php exit();?>".json_encode($data)), array('name' => 'api_ticket'));
			}
		} else {
			$ticket = $data->api_ticket;
		}

		return $ticket;
	}

	//使用会员卡领取的签名包
	public function getHuiYuanSignPackage() {
		$apiTicket = $this->getApiTicket();
		// 注意 URL 一定要动态获取，不能 hardcode.（获取当前网页的url）
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

		//时间戳
		$timestamp = time();
		//随机字符串获取
		$nonceStr = $this->createNonceStr();
//		$code = $this->getVipCodeNumber(11);

		// 这里参数的顺序要按照 key 值 ASCII 码升序排序
		$string = $timestamp.$apiTicket."ptbWuwLwIVb5NnwwpYX64YYr0wpY";
//		$string = $apiTicket.$timestamp."ptbWuwLwIVb5NnwwpYX64YYr0wpY";

		//生成字符串是用来签名用的
		$signature = sha1($string);
		$signPackage = array(
			"timestamp" => $timestamp,
			"signature" => $signature,
		);
		return $signPackage;
	}
}
