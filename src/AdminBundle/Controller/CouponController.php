<?php

namespace AdminBundle\Controller;

use BaseBundle\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class CouponController extends BaseController
{
    /**
     * 优惠券
     */
    public function indexAction()
    {
        $admin_id =$this->getSession('admin_id');
        if (empty($admin_id)) {
            return $this->redirect($this->generateUrl('admin_login'));
        } else {
            $request = $this->get('request');
            $conn = $this->get('database_connection');

            //分配给用户
            $coupon_arr = $conn->fetchAll("SELECT coupon_id	 FROM user_coupon GROUP BY coupon_id");
            $user_coupon = array();
            foreach($coupon_arr as $k => $v){
                $user_coupon[] = $v['coupon_id'];
            }

            //分配给实体卡
            $coupon_arr_two = $conn->fetchAll("SELECT coupon_id FROM offline_card GROUP BY coupon_id");
            $card_coupon = array();
            foreach($coupon_arr_two as $k => $v){
                $card_coupon[] = $v['coupon_id'];
            }

            $coupon = $conn->fetchAll("SELECT * FROM coupon ORDER BY id DESC");

            foreach ($coupon as $k => $v) {
                if (in_array($v['id'], $user_coupon) || in_array($v['id'], $card_coupon) ) { // 已经分配给用户 或者时实体卡
                    $coupon[$k]['is_used'] = 1;
                } else {
                    $coupon[$k]['is_used'] = 0;
                }
            }
        }
        return $this->render('AdminBundle:Coupon:index.html.twig', array('coupon' => $coupon));
    }

    /**
     * 优惠券 添加或编辑
     *
     * @param $id 优惠券的id
     */
    public function editAction($id)
    {
        $admin_id =$this->getSession('admin_id');
        if (empty($admin_id)) {
            return $this->redirect($this->generateUrl('admin_login'));
        } else {
            $request = $this->get('request');
            $conn = $this->get('database_connection');

            if ($request->getMethod() == 'POST') {
                $data['type'] = trim($request->request->get('type'));
                $data['name'] = trim($request->request->get('name'));
                $data['amount'] = trim($request->request->get('amount'));
                $data['day'] = trim($request->request->get('day'));

                if ($data['amount']) {
                    $data['amount'] = $data['amount'];
                } else {
                    $data['amount'] = 0.00;
                }
                if ($id) {
                    $conn->update('coupon', $data, array('id' => $id));
                } else {
                    $conn->insert('coupon', $data);
                }
                return $this->redirect($this->generateUrl('admin_coupon'));
            }
            if ($id) {
                $coupon = $conn->fetchAssoc("SELECT * FROM coupon WHERE id =?", array($id));
            } else {
                $coupon = array();
            }
        }
        return $this->render('AdminBundle:Coupon:edit.html.twig', array('id' => $id, 'coupon' => $coupon));
    }

    /**
     * 优惠券 删除
     *
     * @param $id 优惠券的id
     */
    public function delAction($id)
    {
        $admin_id =$this->getSession('admin_id');
        if (empty($admin_id)) {
            return $this->redirect($this->generateUrl('admin_login'));
        } else {
            $request = $this->get('request');
            $conn = $this->get('database_connection');

            $conn->delete('coupon', array('id' => $id));
        }
        return $this->redirect($this->generateUrl('admin_coupon'));
    }
}
