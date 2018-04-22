<?php

namespace AdminBundle\Controller;

use BaseBundle\Controller\BaseController;
use BaseBundle\Controller\Pagination;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MemberController extends BaseController
{
    /**
     * 会员管理
     */
    public function indexAction()
    {
        $request = $this->get('request');
        $admin_id =$this->getSession('admin_id');

        if (empty($admin_id)) {
            return $this->redirect($this->generateUrl('admin_login'));
        } else {
            $conn = $this->get('database_connection');

            $where = "WHERE 1";
            $url = "";

            if ($request->query->has('nickname') && $request->query->get('nickname')) {
                $filter['nickname'] = $request->query->get('nickname');
                $url .= '&nickname='.$filter['nickname'];
                $where .= " AND u.nickname LIKE '%". $filter['nickname'] ."%' ";
            } else {
                $filter['nickname'] = "";
            }

            if ($request->query->has('phone') && $request->query->get('phone') >= 0) {
                $filter['phone'] = $request->query->get('phone');
                $url .= '&phone='.$filter['phone'];
                $where .= " AND u.phone LIKE '%". $filter['phone'] ."%' ";
            } else {
                $filter['phone'] = "";
            }

            if ($request->query->has('rank') && $request->query->get('rank')) {
                $filter['rank'] = $request->query->get('rank');
                $url .= '&rank='.$filter['rank'];
                $where .= " AND u.rank = ". $filter['rank'];
            } else {
                $filter['rank'] = "";
            }

            if ($request->query->has('group_id') && $request->query->get('group_id')) {
                $filter['group_id'] = $request->query->get('group_id');
                if ($filter['group_id'] == -1) {
                    $group_id = 0;
                    $url .= '&group_id='.$filter['group_id'];
                    $where .= " AND u.group_id = ". $group_id;
                } else {
                    $url .= '&group_id=' . $filter['group_id'];
                    $where .= " AND u.group_id = ". $filter['group_id'];
                }
            } else {
                $filter['group_id'] = "";
            }
            $request =$this->get('request');
            if ($request->query->has('page') && (int)$request->query->get('page')) {
                $page = (int)$request->query->get('page');
            } else {
                $page = 1;
            }

            if ($request->getMethod() == 'POST') {
                $limit = trim($request->request->get('limit'));
            } else {
                $limit = 10;
            }

            $start = ($page - 1) * $limit;

            $user = $conn->fetchAll("SELECT u.*, up.name AS group_name FROM user u LEFT JOIN user_group up ON u.group_id = up.id $where AND rank > 0 ORDER BY u.register_date DESC LIMIT $start,$limit");
            $total = $conn->fetchColumn ("SELECT COUNT(u.id) FROM user u $where AND rank > 0");
            $user_coupon_arr = $conn->fetchAll("SELECT uc.user_id, COUNT(uc.id) AS coupon_number FROM user_coupon uc  GROUP BY uc.user_id");

            $user_coupon = array();
            foreach ($user_coupon_arr  as $k => $v){
                $user_coupon[$v['user_id']] = $v['coupon_number'];
            }

            $pagination = new Pagination();
            $pagination->total = $total;
            $pagination->page = $page;
            $pagination->limit = $limit;
            $pagination->url = $this->generateUrl('admin_member') . '?page={page}'.$url;

            $render = $pagination->render();

            $group = $conn->fetchAll("SELECT * FROM user_group");

        }

        return $this->render('AdminBundle:Member:index.html.twig', array('user' => $user, 'filter' => $filter, 'render' => $render, 'limit' => $limit, 'group' => $group, 'user_coupon' => $user_coupon));
    }


    /**
     * 用户分组管理
     */
    public function memberGroupAction()
    {
        $admin_id =$this->getSession('admin_id');
        if(empty($admin_id)){
            return $this->redirect($this->generateUrl('admin_login'));
        }

        $request = $this->get('request');
        $conn = $this->get('database_connection');

        if($request->getMethod() == "POST") {
            $name = $request->request->get('name');

            $conn->insert('user_group', array('name' => $name));

            return $this->redirect($this->generateUrl('admin_member_group'));
        }

        $user_group = $conn->fetchAll("SELECT * FROM user_group");

        return $this->render('AdminBundle:Member:member_group.html.twig', array('user_group' => $user_group));
    }


    /**
     * 编辑用户分组
     *
     * @param $id 分组ID
     * @return Response
     */
    public function memberGroupEditAction($id)
    {
        $request = $this->get('request');
        $admin_id =$this->getSession('admin_id');
        if(empty($admin_id)){
            $result = array('flag' => 2, 'msg' => '请先登录');
        }else{
            $name = $request->request->get('name');

            if($name && $id){
                $conn = $this->get('database_connection');
                $conn->update("user_group", array('name' => $name), array('id' => $id));

                $result = array('flag' => 1, 'msg' => '修改成功');
            }else{
                $result = array('flag' => 2, 'msg' => '请先正确输入参数');
            }
        }

        return new Response(json_encode($result));
    }

    /**
     * 删除用户分组
     *
     * @param $id 分组ID
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function memberGroupDelAction($id)
    {
        $admin_id =$this->getSession('admin_id');
        if(empty($admin_id)){
            return $this->redirect($this->generateUrl('admin_login'));
        }

        $conn = $this->get('database_connection');
        $conn->update('user', array('group_id' => 0), array('group_id' => $id));
        $conn->delete('user_group', array('id' => $id));

        return $this->redirect($this->generateUrl('admin_member_group'));
    }


    /**
     * 设置分组
     */
    public function setMemberGroupAction()
    {
        $request = $this->get('request');
        $admin_id =$this->getSession('admin_id');
        if(empty($admin_id)){
            $result = array('flag' => 2, 'msg' => '请先登录');
        }else{
            $group_id = $request->request->get('group_id');
            $id = $request->request->get('id');
            $id_arr = explode(',', $id);

            $conn = $this->get('database_connection');
            $group = $conn->fetchAssoc("SELECT * FROM user_group WHERE id = ?", array($group_id));

            if(empty($group)){
                $result = array('flag' => 2, 'msg' => '请正确选择分组');
            }elseif(empty($id)){
                $result = array('flag' => 2, 'msg' => '请正确选择会员');
            }else{
                foreach($id_arr as $v){
                    $conn->update("user", array('group_id' => $group_id), array('id' => $v));
                }

                $result = array('flag' => 1, 'msg' => '会员分组设置成功');
            }
        }

        return new Response(json_encode($result));
    }

    /**
     * 赠送优惠券
     */
    public function giveCouponAction()
    {
        $request = $this->get('request');
        $admin_id =$this->getSession('admin_id');
        if(empty($admin_id)){
            $result = array('flag' => 2, 'msg' => '请先登录');
        }else{
            $click_number = $request->request->get('click_number');
            $coupon_id = $request->request->get('coupon_id');
            $id = $request->request->get('id');
            $id_arr = explode(',', $id);

            $conn = $this->get('database_connection');
            $coupon = $conn->fetchAssoc("SELECT * FROM coupon WHERE id = ?", array($coupon_id));
            $coupon_day = $coupon['day'];
            $date = date('Y-m-d H:i:s');

            $storesOwned = $conn->fetchColumn("SELECT storesOwned FROM admin WHERE id = ?", array($admin_id));

            if ($click_number > 1) {
                $result = array('flag' => 2, 'msg' => '请勿重复操作');
            } else {
                if(empty($coupon)){
                    $result = array('flag' => 2, 'msg' => '请正确填写优惠券ID');
                }elseif(empty($id)){
                    $result = array('flag' => 2, 'msg' => '请正确选择赠送会员');
                }else{
                    foreach($id_arr as $v){
                        $data = array(
                            'coupon_id' => $coupon['id'],
                            'coupon_number' => $this->getCouponNumber($admin_id),
                            'user_id' => $v,
                            'is_used' => 0,
                            'add_date' => $date,
                            'end_date' =>  date('Y-m-d H:i:s',strtotime("$date +$coupon_day day")),
                            'admin_id' =>  $admin_id,
                            'storesOwned' =>  $storesOwned
                        );
                        $conn->insert("user_coupon", $data);
                        $coupon_id = $conn->lastInsertId();
                        $this->sendCouponWX($coupon_id, 1);
                    }

                    $result = array('flag' => 1, 'msg' => '优惠券赠送成功');
                }
            }
        }
        return new JsonResponse($result);
    }

    /**
     * 赠送积分
     */
    public function givePointsAction()
    {
        $request = $this->get('request');
        $admin_id =$this->getSession('admin_id');
        $store_id =$this->getSession('power');
        if(empty($admin_id)){
            $result = array('flag' => 2, 'msg' => '请先登录');
        }else{
            $click_number = $request->request->get('click_number');
            $points = $request->request->get('points');
            $id = $request->request->get('id');
            $id_arr = explode(',', $id);

            $conn = $this->get('database_connection');

            if ($click_number > 1) {
                $result = array('flag' => 2, 'msg' => '请勿重复操作');
            } else {
                if(empty($points)){
                    $result = array('flag' => 2, 'msg' => '请正确填写积分');
                }elseif(empty($id)){
                    $result = array('flag' => 2, 'msg' => '请正确选择赠送会员');
                }else{
                    foreach($id_arr as $v){
                        $user_points = $conn->fetchColumn("SELECT points FROM user WHERE id = ?", array($v));
                        $new_points = (int)$user_points + $points;
                        $data = array(
                            'points' => $new_points
                        );
                        $conn->update("user", $data, array('id' => $v));
                        $consumption = array(
                            'amount' => 0.00,
                            'point_type' => 1,
                            'point_limit' => $points,
                            'admin_id' => $admin_id,
                            'storesOwned' => $store_id,
                            'add_date' => date('Y-m-d H:i:s'),
                            'coupon_id' => 0,
                            'mark' => '后台管理员批量赠送积分',
                            'user_id' => $v
                        );
                        $conn->insert("user_consumption", $consumption);
                        $consumption_id = $conn->lastInsertId();
                        $this->sendWX($consumption_id);
                    }
                    $result = array('flag' => 1, 'msg' => '积分赠送成功');
                }
            }
            
        }

        return new Response(json_encode($result));
    }

    /**
     * 会员管理修改积分
     *
     * @param $id 用户的id
     */
    public function changePointsAction()
    {
        $request = $this->get('request');
        $admin_id =$this->getSession('admin_id');
        $currentUrl = $request->getUri();
        if(empty($admin_id)){
            return $this->redirect($this->generateUrl('admin_login', array('currentUrl'=>$currentUrl)));
        }

        $conn = $this->get('database_connection');
        $error = array('flag' => 0, 'content' => '');

        $id = $request->query->get('user_id');
        $user = $conn->fetchAssoc("SELECT id, name, phone, points FROM user WHERE id = ?", array($id));

        if($request->getMethod() == "POST") {
            $data['point_type'] = $request->request->get('point_type');
            $data['point_limit'] = $request->request->get('point_limit');
            $data['amount'] = $request->request->get('amount');
            $data['mark'] = $this->filterExpression($request->request->get('mark'));
            $data['user_id'] = $request->request->get('user_id');

            $coupon_number = $request->request->get('coupon_number');

            $user = $conn->fetchAssoc("SELECT id, name, phone, points FROM user WHERE id = ?", array($data['user_id']));

            if ($user['points'] <= 0 && $data['point_type'] == 2) {
                $error = array('flag' => 1, 'content' => '该用户积分为0，不能再减少积分了');
                return new JsonResponse($error);
            }

            if ($data['point_limit'] > $user['points'] && $data['point_type'] == 2) {
                $error = array('flag' => 6, 'content' => '减少积分额度大于实际积分');
                return new JsonResponse($error);
            }

            if (empty($data['point_type']) ) {
                $error = array('flag' => 1, 'content' => '参数有误，请重新输入');
                return new JsonResponse($error);
            }

            if ($coupon_number) {
                $coupon = $conn->fetchAssoc("SELECT coupon_id, coupon_number, is_used, end_date FROM user_coupon WHERE coupon_number = ?", array($coupon_number));                

                if (empty($coupon)) {
                    $error = array('flag' => 2, 'content' => '该优惠券不存在');
                    return new JsonResponse($error);
                } elseif ($coupon['is_used'] == 1) {
                    $error = array('flag' => 3, 'content' => '该优惠券已使用');
                    return new JsonResponse($error);
                } elseif ($coupon['end_date'] < date('Y-m-d H:i:s')) {
                    $error = array('flag' => 4, 'content' => '该优惠券已过期');
                    return new JsonResponse($error);
                }
                // 优惠券类型
                $coupon_type = $conn->fetchColumn("SELECT type FROM coupon WHERE id = ?", array($coupon['coupon_id']));
//                else {
//                    $coupon_amount = $conn->fetchColumn("SELECT amount FROM coupon WHERE id = ?", array($coupon['coupon_id']));
//                    if ($coupon_amount >= $data['amount']) {
//                        $error = array('flag' => 5, 'content' => '该优惠券的金额大于实际交易金额，请重新更换优惠券');
//                        return new JsonResponse($error);
//                    }
//                }

            } else {
                $coupon = array('coupon_id' => 0);
                $coupon_amount = 0;
                $coupon_type = 0;
            }

            $storesOwned = $conn->fetchColumn("SELECT storesOwned FROM admin WHERE id = ?", array($admin_id));
            $data['coupon_id'] = $coupon['coupon_id'];
            $data['admin_id'] = $admin_id;
            $data['add_date'] = date('Y-m-d H:i:s');
            $data['storesOwned'] = $storesOwned;

            //记录消费
            $flag_one = $conn->insert('user_consumption', $data);
            $consumption_id = $conn->lastInsertId();
            //更改用户表积分记录
            if ($data['point_type'] == 1) {
                $points = (int)$user['points'] + $data['point_limit'];
            } elseif ($data['point_type'] == 2) {
                $points = (int)$user['points'] - $data['point_limit'];
            }
            $flag_two = $conn->update('user', array('points' => $points), array('id' => $data['user_id']));
            //如果使用优惠券，则该优优惠券失效
            if ($coupon_number && $coupon_type > 0) {
                $flag_there = $conn->update('user_coupon', array('is_used' => 1), array('coupon_number' => $coupon_number));
                // 优惠券推送消息
                $this->sendCouponWX($consumption_id, 2);
            }
            // 积分推送消息
            $this->sendWX($consumption_id);

            $error = array('flag' => 0, 'content' => '交易成功');
            return new JsonResponse($error);

        }

        return $this->render('AdminBundle:Member:points_change.html.twig', array('user' => $user, 'error' => $error));
    }

    /**
     * 会员管理 积分详情
     *
     * @param $id 用户的id
     */
    public function pointsAction($id)
    {
        $admin_id =$this->getSession('admin_id');
        if(empty($admin_id)){
            return $this->redirect($this->generateUrl('admin_login'));
        }

        $request = $this->get('request');
        $conn = $this->get('database_connection');

        $points = $conn->fetchAll("SELECT uc.*, s.name AS store_name, a.account, a.storesOwned FROM user_consumption uc LEFT JOIN admin a ON uc.admin_id = a.id LEFT JOIN store s ON s.id = uc.storesOwned WHERE uc.user_id = ? ORDER BY uc.add_date DESC ", array($id));


        return $this->render('AdminBundle:Member:points.html.twig', array('points' => $points));
    }

    /**
     * 会员管理 优惠券详情
     *
     * @param $id 用户的id
     */
    public function couponAction($id)
    {
        $admin_id =$this->getSession('admin_id');
        if(empty($admin_id)){
            return $this->redirect($this->generateUrl('admin_login'));
        }

        $request = $this->get('request');
        $conn = $this->get('database_connection');

        $coupon = $conn->fetchAll("SELECT uc.id, uc.is_used, c.name AS coupon_name, c.amount, c.type, s.name AS store_name, a.account, a.storesOwned, uc.add_date, uc.end_date FROM user_coupon uc LEFT JOIN coupon c ON c.id = uc.coupon_id LEFT JOIN admin a ON uc.admin_id = a.id LEFT JOIN store s ON s.id = uc.storesOwned WHERE uc.user_id = ? ORDER BY uc.add_date DESC ", array($id));

        foreach ($coupon as $k => $v) {
            if ($v['is_used'] == 0 && $v['end_date'] > date('Y-m-d H:i:s')) {
                $coupon[$k]['is_ok'] = '待使用';
            } elseif ($v['is_used'] == 0 && $v['end_date'] <= date('Y-m-d H:i:s')) {
                $coupon[$k]['is_ok'] = '已过期';
            } elseif ($v['is_used'] == 1 ) {
                $coupon[$k]['is_ok'] = '已使用';
            }
        }

        return $this->render('AdminBundle:Member:coupon.html.twig', array('coupon' => $coupon));
    }

}
