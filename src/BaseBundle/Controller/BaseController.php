<?php

namespace BaseBundle\Controller;

use Endroid\QrCode\QrCode;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use BaseBundle\Controller\WXApi\WxApi;


class BaseController extends Controller
{
    /**
     * 设置session
     *
     * @param type $data 数组
     */
    public function setSession($data)
    {
        $session = $this->getRequest()->getSession();

        foreach ($data as $sessionName => $sessionValue) {
            $session->set($sessionName, $sessionValue);
        }

    }

    /**
     * 获取session
     * @param type $sessionName
     * @return type
     */
    public function getSession($sessionName)
    {
        $session = $this->getRequest()->getSession();
        $sessionValue = $session->get($sessionName);

        return $sessionValue;
    }

    /**
     * 清空session
     */
    public function clearSession()
    {
        $this->getRequest()->getSession()->clear();
    }

    /**
     * 上传图片
     *
     * @param type $str 这个是表单上file类型的input的name值
     * @param type $path 上传路径
     * @return str 附件的全名，包括扩展名
     */
    public function fileUpload($str, $newFileName = "", $path = '')
    {
        $result = '';

        $fs = new Filesystem();
        if (!$fs->exists($path)) {
            $fs->mkdir($path, 0777);
        }

        $storage = new \Upload\Storage\FileSystem($path);
        $file = new \Upload\File($str, $storage);
        if ($file->getName() != '') {
            if (empty($newFileName)) {
                $newFileName = uniqid();
                $file->setName($newFileName);
            } else {
                $file->setName($newFileName);
            }

            $file->addValidations(array(
                new \Upload\Validation\Size('100M')
            ));

            try {
                $file->upload();
                $result = $file->getNameWithExtension();
            } catch (\Exception $e) {

            }
        }

        return $result;
    }

    /**
     * 上传图片
     *
     * @param $inputName 这个是表单上file类型的input的name值
     * @param $path 图片上传路径
     * @param $filename 图片名称，不包括扩展名
     * @param array $format 图片类型
     * @param int $size 图片尺寸
     * @return array 图片的全名，包括扩展名
     */
    public function uploadImage($inputName, $path, $filename, $format = array('image/png', 'image/gif', 'image/jpg', 'image/jpeg'), $size = 2)
    {
        $result = array();
        $result['errors'] = array('上传失败');

        if (!file_exists($path)) {
            mkdir($path, 0755);
        }

        $storage = new \Upload\Storage\FileSystem($path);
        $file = new \Upload\File($inputName, $storage);

        if ($file->getSize() > $size * 1024 * 1024) {
            $result['errors'] = array('上传图片不得超过' . $size . 'M');
        } else {
            if ($file->getName() != '') {//有新的图片上传
                $file->setName($filename);

                // Validate file upload
                // MimeType List => http://www.webmaster-toolkit.com/mime-types.shtml
                $file->addValidations(array(
                    new \Upload\Validation\Mimetype($format),
                    new \Upload\Validation\Size($size . 'M')
                ));

                try {
                    $file->upload();
                    $result['name'] = $file->getNameWithExtension();
                } catch (\Exception $e) {
                    $result['errors'] = $file->getErrors();
                }
            }
        }

        return $result;
    }

    /**
     * 上传图片
     *
     * @return JsonResponse
     */
    public function uploadImageAction()
    {
        if (isset($_FILES['image']) && $_FILES['image']['name']) {
            $date = date('Ymd');
            $path = $this->getParameter('dir_upload') . DIRECTORY_SEPARATOR . 'avatar_update/' . DIRECTORY_SEPARATOR;
            $data = $this->uploadImage('image', $path, uniqid());

            if (isset($data['name'])) {
                $result = array('errorCode' => 0, 'message' => '', 'data' => array('path' => 'avatar_update/' . $data['name'], 'filename' => $data['name']));
            } else {
                $result = array('errorCode' => 1, 'message' => current($data['errors']));
            }
        } else {
            $result = array('errorCode' => 2, 'message' => '请上传图片');
        }

        return new JsonResponse($result);
    }

    /**
     * 过滤表情
     *
     * 对于短文本中的表情进行处理
     *
     * @param $content
     * @return mixed
     */
    public function filterExpression($content)
    {
        $content = preg_replace_callback(
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $content
        );


        return preg_replace('~<img(.*?)>~s','',$content);
    }

    /**
     * 生成优惠券编号
     *
     * @param $user_id 用户id
     */
    public function getCouponNumber($user_id)
    {
        return "YY".$user_id.substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }

    /**
     * 生成实体卡编号
     *
     * @param $admin_id 用户id
     */
    public function getCardsNumber($admin_id)
    {
        return "YY".$admin_id.substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 11);
    }

    /**
     * 生成实体卡子卡编号
     *
     * @param $card_id 实体卡id, $length 生成多少个子卡
     */
    public function getCardNumber($card_id, $length)
    {
        $result = array();
        $count = '';

        if ($card_id < 10 && $card_id > 0) {
            $card_id = '00'.$card_id;
        } elseif ($card_id >= 10 && $card_id < 100) {
            $card_id = '0'.$card_id;
        }

        while ($count < $length) {
            $result[] = 'YY'.$card_id.substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 11);
            $result = array_unique($result);
            $count=count($result);
        }
        sort($result);
        return $result;
    }

    /**
     * 生成code编号
     *
     * @param $user_id 用户id
     */
    public function getVipCodeNumber($user_id)
    {
        return $user_id.substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }

    /**
     * 二维码
     *
     * @param string $text  内容（网址）
     * @param int $size  大小
     * @param string $title 标题
     */
    public function qrcode($text = 'http://www.baidu.com', $size = 300, $title = '')
    {
        $qrCode = new QrCode();
        $qrCode
            ->setText($text)
            ->setSize($size)
            ->setPadding(10)
            ->setErrorCorrection('high')
            ->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0])
            ->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0])
            ->setLabel($title)
            ->setLabelFontSize(16)
            ->setImageType(QrCode::IMAGE_TYPE_PNG)
        ;

        // now we can directly output the qrcode
        header('Content-Type: '.$qrCode->getContentType());
        $qrCode->render();
    }


    /**
     * 生成二维码控制器
     *
     * @return Response $user_id 用户id $coupon_number优惠券编号
     */
    public function qrCodeAction($user_id, $coupon_number)
    {
        $request = $this->get('request');
        if ($coupon_number == 1) {
            $coupon_number = '';
        }
        $siteUrl = urldecode($this->generateUrl('admin_member_change_points', array('user_id' => $user_id, 'coupon_number' => $coupon_number)));
        $siteUrl = $request->getSchemeAndHttpHost() . $siteUrl;
        $data = $this->qrcode($siteUrl);

        return new JsonResponse($data);
    }


    /**
     * 发邮件
     *
     * @return Response
     */
    public function sendmail($password)
    {
        require_once dirname(__FILE__) . "/smtp.php";
        $request = $this->get('request');
        //使用163邮箱服务器
        $smtpserver = "smtp.exmail.qq.com";
        //163邮箱服务器端口
        $smtpserverport = 25;
        //你的163服务器邮箱账号
        $smtpusermail = "yangying@zmit.cn";
        //收件人邮箱
        $smtpemailto = "zhouyou0101@163.com";
        //你的邮箱账号(去掉@163.com)
        $smtpuser = "zhouyou0101@163.com";//SMTP服务器的用户帐号
        //你的邮箱密码
        $smtppass = "bison1989"; //SMTP服务器的用户密码
        //邮件主题
        $mailsubject = "摇叶超级管理员找回密码";
        //邮件内容
        $name = $password;

        $mailbody = ' 密码为：'.$name.'请妥善保管';
        
        //邮件格式（HTML/TXT）,TXT为文本邮件
        $mailtype = "TXT";
        //这里面的一个true是表示使用身份验证,否则不使用身份验证.
        $smtp = new \smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);
        //是否显示发送的调试信息
        $smtp->debug = TRUE;
        //发送邮件
        $smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype);

        return $smtp;
    }

    /**
     * 获取用户微信openid
     *
     * @param $to_url
     * @return WXApi\用户的openid
     */
    public function getOpenId($to_url)
    {
        $request = $this->get('request');

        require_once __DIR__ . "/WXApi/WxApi.php";

        $tools = new WxApi();
        $openId = $tools->GetOpenid($request->getSchemeAndHttpHost() . $to_url);

        return $openId;
    }

    /**
     * 获取用户微信access_token
     *
     * @return WXApi\用户的access_token
     */
    public function getAccessToken()
    {
        require_once __DIR__ . "/WXApi/WxApi.php";

        $tools = new WxApi();
        $token = $tools->GetAccessTokenFromMp();

        return $token;
    }

    /**
     * 获取用户微信access_token是否失效
     *
     * @return WXApi\用户的access_token
     */
    public function getAccessTokenTime()
    {
        $conn  = $this->get('database_connection');
        $old_token = $conn->fetchAssoc("SELECT * FROM access_token ORDER BY id DESC");
        if(time() > $old_token['expire'] + 3600){
            $token = $this->getAccessToken();
            if($token){
                $conn->insert('access_token', array('token' => $token, 'expire' => time()));
            }
        }else{
            $token = $old_token['token'];
        }
        return $token;
    }

    /**
     * 判断是否关注及获取微信信息
     *
     * @return array
     */
    public function judgeFollow()
    {
        $request = $this->get('request');
        $conn  = $this->get('database_connection');
        if( empty($this->getSession('openid')) ){
            $from_url = $request->query->get('from_url');
            $user = $this->getOpenId($this->generateUrl('user_index') . '?from_url=' . urlencode($from_url));
            $openid = $user['openid'];
        } else {
            $openid = $this->getSession('openid');
        }
        $token = $this->getAccessTokenTime();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$token&openid=$openid&lang=zh_CN",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"area\"\r\n\r\n340000,340200,340201\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;exit;
        } else {
            $return = json_decode($response, true);
            $is_follow = $return['subscribe'];
        }

        return $is_follow;
    }

    /**
     * 微信推送消息 (积分推送)
     *
     * @param $id 积分变动ID
     */
    public function sendWX($id)
    {
        $conn  = $this->get('database_connection');

        $token = $this->getAccessTokenTime();

        $request = $this->get('request');

        $order = $conn->fetchAssoc("SELECT u.name, s.name AS store_name, u.openid, u.phone, u.points, uc.point_type, uc.point_limit  FROM user_consumption uc LEFT JOIN user u ON uc.user_id = u.id LEFT JOIN store s ON s.id = uc.storesOwned WHERE uc.id = ?", array($id));

        $url = $request->getSchemeAndHttpHost() . $this->generateUrl('user_personal');

        if ($order['store_name'] == '') {
            $account_name = '摇叶茶饮';
        } else {
            $account_name = $order['store_name'];
        }
        $first = "您好，您的会员积分信息有了新的变更。";
        $name = $order['name'];
        $account = $account_name;
        if ($order['point_type'] == 1) {
            $points_change = "您有".$order['point_limit']."积分入户哦！";
        } else {
            $points_change = "您的帐号减少".$order['point_limit']."积分哦！";
        }
        $points = $order['points'];
        $remark = "摇叶与你一起热爱生活\\n点击详情反馈您的消费体验，将帮助我们更好的提升服务品质";

        $openid = $order['openid'];

        $data = "{
           \"touser\":\"$openid\",
           \"template_id\":\"WUxl2heVb65VgDnuqWGsupzFKU-FKta7R9pBQM7G7tI\",
           \"url\":\"$url\",
           \"data\":{
                   \"first\": {
                       \"value\":\"$first\",
                       \"color\":\"#000000\"
                   },
                   \"keyword1\":{
                       \"value\":\"$name\",
                       \"color\":\"#000000\"
                   },
                   \"keyword2\": {
                       \"value\":\"$account\",
                       \"color\":\"#000000\"
                   },
                   \"keyword3\": {
                       \"value\":\"$points_change\",
                       \"color\":\"#000000\"
                   },
                   \"keyword4\": {
                       \"value\":\"$points\",
                       \"color\":\"#000000\"
                   },
                   \"remark\":{
                       \"value\":\"$remark\",
                       \"color\":\"#000000\"
                   }
           }
        }";

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$token,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "postman-token: 567b4a00-e813-a0fb-5516-428dbcffa5c0"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

//        if ($err) {
//            echo "cURL Error #:" . $err;
//        } else {
//            echo $response;
//        }

    }

    /**
     * 微信推送消息 (优惠券添加和核销)
     *
     * @param $id 优惠券添加ID 和核销的id
     * $is_type 分辨添加优惠券还是核销优惠券  $is_type：1 添加 2：核销
     */
    public function sendCouponWX($id, $is_type)
    {
        $conn  = $this->get('database_connection');

        $token = $this->getAccessTokenTime();

        $request = $this->get('request');
        if ($is_type == 1) {
            $order = $conn->fetchAssoc("SELECT uc.coupon_id, uc.add_date, s.name AS store_name, u.openid FROM user_coupon uc LEFT JOIN user u ON uc.user_id = u.id LEFT JOIN store s ON s.id = uc.storesOwned WHERE uc.id = ?", array($id));
        } else {
            $order = $conn->fetchAssoc("SELECT uc.coupon_id, uc.add_date, s.name AS store_name, u.openid FROM user_consumption uc LEFT JOIN user u ON uc.user_id = u.id LEFT JOIN store s ON s.id = uc.storesOwned WHERE uc.id = ?", array($id));
        }

        $coupon = $conn->fetchAssoc("SELECT c.name, c.amount, c.type FROM coupon c WHERE c.id = ? ", array($order['coupon_id']));

        $url = $request->getSchemeAndHttpHost() . $this->generateUrl('user_personal');

        if ($is_type == 1) {
            $first = "您好，您的优惠券已经成功添加。";
        } elseif ($is_type == 2) {
            $first = "您好，您的优惠券已经成功使用。";
        }

        if ($order['store_name'] == '') {
            $account_name = '摇叶';
        } else {
            $account_name = $order['store_name'];
        }

        $name = $account_name;

        $time = $order['add_date'];

        $coupon_name = $coupon['name'];
        
        if ($coupon['type'] == 1) {
            $amount = $coupon['amount'].'元';
        } else {
            $amount = '无';
        }

        $remark = "摇叶与你一起热爱生活\\n点击详情反馈您的消费体验，将帮助我们更好的提升服务品质";

        $openid = $order['openid'];

        $data = "{
           \"touser\":\"$openid\",
           \"template_id\":\"sSYrmyd-KsFTDFigfYsEMPo-i4GnT1Rxh2uiSxr4dzk\",
           \"url\":\"$url\",
           \"data\":{
                   \"first\": {
                       \"value\":\"$first\",
                       \"color\":\"#000000\"
                   },
                   \"keyword1\":{
                       \"value\":\"$name\",
                       \"color\":\"#000000\"
                   },
                   \"keyword2\": {
                       \"value\":\"$time\",
                       \"color\":\"#000000\"
                   },
                   \"keyword3\": {
                       \"value\":\"$coupon_name\",
                       \"color\":\"#000000\"
                   },
                   \"keyword4\": {
                       \"value\":\"$amount\",
                       \"color\":\"#000000\"
                   },
                   \"remark\":{
                       \"value\":\"$remark\",
                       \"color\":\"#000000\"
                   }
           }
        }";

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$token,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "postman-token: 567b4a00-e813-a0fb-5516-428dbcffa5c0"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
//
//        if ($err) {
//            echo "cURL Error #:" . $err;
//        } else {
//            echo $response;
//        }

    }


    /**
     * 微信jssdk 配置字段 内置数据
     */
    public function jsapiTicketAction()
    {
        $admin_id = $this->getSession('admin_id');

        $request = $this->get('request');
        $conn = $this->get('database_connection');

        $is_add = $conn->fetchColumn("SELECT id FROM config WHERE name = 'jsapi_ticket'");
        if ($is_add) {
        } else {
            $conn -> insert('config', array('value' => "", 'name' => 'jsapi_ticket'));
        }
        return $this->redirect($this->generateUrl('user_homepage'));
    }

}
