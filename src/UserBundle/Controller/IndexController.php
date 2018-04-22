<?php

namespace UserBundle\Controller;

use BaseBundle\Controller\BaseController;
use BaseBundle\Controller\JssdkController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class IndexController extends JssdkController
{
    /**
     * 领取会员卡
     */
    public function indexAction()
    {
        $request = $this->get('request');
        $conn = $this->get('database_connection');

        $user_id =$this->getSession('user_id');
        if ($user_id) {
            return $this->redirect($this->generateUrl('user_personal'));
        } else {
            if ($request->getMethod()=='POST') {
                $phone = trim($request->request->get('phone'));

                $user = $conn->fetchAssoc("SELECT id, name, openid, avatar FROM user WHERE phone =?", array($phone));
                if ($user) {
                    $data = array(
                        'user_id' => $user['id'],
                        'name' => $user['name'],
                        'openid' => $user['openid'],
                        'avatar' => $user['avatar']
                    );
                    $this->setSession($data);
                } else {
                    $conn->insert("user", array('phone' => $phone, 'openid' => rand(10000,99999), 'points' => 0, 'register_date' => date('Y-m-d H:i:s'), 'rank' => 1));
                    $data['user_id'] = $conn->lastInsertId();
                    $this->setSession($data);
                }
                return $this->redirect($this->generateUrl('user_personal'));
            }
        }

        return $this->render('UserBundle:Index:index.html.twig');
    }

    /**
     * 领取或者没有领取会员卡
     *
     * @param $type type:1 领取 2：没有领取
     * @return Response
     */
    public function cardEditAction($type)
    {
        $request = $this->get('request');
        $conn = $this->get('database_connection');
        $user_id =$this->getSession('user_id');
        if(empty($user_id)){
            $result = array('flag' => 2, 'msg' => '请先领取会员卡');
        }else{
            $phone = trim($request->request->get('phone'));
            if ($type == 1) {
                // 赠送新用户优惠券 优惠券id为1.2.3
                $coupon_id = 1;
                while ( $coupon_id < 4) {
                    $coupon_day = $conn->fetchColumn("SELECT day FROM coupon WHERE id =?", array($coupon_id));
                    $coupon['coupon_id'] = $coupon_id;
                    $coupon['coupon_number'] =  $this->getCouponNumber($user_id);
                    $coupon['user_id'] = $user_id;
                    $coupon['is_used'] = 0;
                    $start_date = date('Y-m-d H:i:s');
                    $coupon['add_date'] = $start_date;
                    $coupon['end_date'] = date('Y-m-d H:i:s',strtotime("$start_date + $coupon_day day"));

                    $conn->insert('user_coupon', $coupon);
                    $coupon_id ++;
                }
                $conn->update('user', array('phone' => $phone, 'rank' => 1), array('id' => $user_id));
            } else {
                $phone = '';
                // 删除 赠送新用户优惠券 优惠券id为1.2.3
                $conn->delete('user_coupon', array('user_id' => $user_id));

                $conn->update('user', array('phone' => $phone, 'rank' => 0), array('id' => $user_id));
            }
            
            $result = array('flag' => 1, 'msg' => '修改成功');
        }

        return new JsonResponse($result);
    }


    /**
     * 验证手机号
     *
     */
    public function phoneAction()
    {
        $request = $this->get('request');
        $conn = $this->get('database_connection');
        $user_id =$this->getSession('user_id');
        $result = array('flag' => 0, 'msg' => '');
        if(empty($user_id)){
            $result = array('flag' => 2, 'msg' => '请先领取会员卡');
        }else{
            $phone = trim($request->request->get('phone'));
            //验证手机号是否已被注册
            $is_phone = $conn->fetchColumn("SELECT id FROM user WHERE phone =?", array($phone));
            if ($is_phone) {
                $result = array('flag' => 1, 'msg' => '该手机号已被使用');
            } else {
                $result = array('flag' => 0, 'msg' => '');
            }
        }
        return new JsonResponse($result);
    }

}
