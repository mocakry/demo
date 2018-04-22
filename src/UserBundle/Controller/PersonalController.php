<?php

namespace UserBundle\Controller;

use BaseBundle\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class PersonalController extends BaseController
{
    /**
     * 会员中心
     */
    public function indexAction()
    {
        $user_id =$this->getSession('user_id');
//        $user_id =5;
        if (empty($user_id)) {
            return $this->redirect($this->generateUrl('user_index'));
        } else {
            $conn = $this->get('database_connection');

            $user = $conn->fetchAssoc("SELECT id, avatar, avatar_update, name, points FROM user WHERE id =?", array($user_id));

            $coupon = $conn->fetchColumn("SELECT COUNT(id) FROM user_coupon WHERE user_id =? AND is_used = 0 AND end_date >= NOW()", array($user_id));

        }
        return $this->render('UserBundle:Personal:index.html.twig', array('user' => $user, 'coupon' => $coupon));
    }

    /**
     * 充值
     */
    public function rechargeAction()
    {
        $result = array('flag' => 0, 'msg' => '');
        $user_id =$this->getSession('user_id');
        if (empty($user_id)) {
            $result = array('flag' => 1, 'msg' => '请先登录');
        } else {
            $request = $this->get('request');
            $conn = $this->get('database_connection');

            $vip_number = trim($request->request->get('vip_number'));
            //  查看该会员卡是否存在
            $is_have = $conn->fetchAssoc("SELECT * FROM sub_card WHERE Sub_card_id =? ", array($vip_number));
            if (empty($is_have['id'])) {
                $result = array('flag' => 2, 'msg' => '该会员卡号不存在，请重新输入');
            } elseif ($is_have['is_used'] == 1) {
                $result = array('flag' => 2, 'msg' => '该会员卡号已使用，请重新输入');
            } else {

                //获得该会员卡的优惠券 (先获得父实体卡， 再获得优惠券id和个数)
                $parent_card_rank = $conn->fetchColumn("SELECT rank FROM offline_card WHERE card_id =?", array($is_have['card_id']));

                $coupon_card = $conn->fetchAll("SELECT oc.id, oc.coupon_id, oc.coupon_number FROM offline_card oc WHERE oc.card_id = ?", array($is_have['card_id']));

                // 插入充值记录
                $conn ->insert('card_record', array('user_id' => $user_id, 'sub_card_id' => $vip_number));
                //更新子卡表，该卡已使用
                $conn ->update('sub_card', array('is_used' => 1), array('Sub_card_id' => $vip_number));
                // 更新等级
                $conn ->update("user", array('rank'=>$parent_card_rank), array('id' => $user_id));

                // 更新用户的优惠券
                foreach ($coupon_card as $value) {
                    $coupon_day = $conn->fetchColumn("SELECT day FROM coupon WHERE id =? ", array($value['coupon_id']));
                    $count = 0;
                    $date = date('Y-m-d H:i:s');
                    while ( $count < $value['coupon_number']) {
                        $data['coupon_id'] = $value['coupon_id'];
                        $data['coupon_number'] =  $this->getCouponNumber($user_id);
                        $data['user_id'] = $user_id;
                        $data['is_used'] = 0;
                        $start_date = date('Y-m-d H:i:s');
                        $data['add_date'] = $start_date;
                        $data['end_date'] = date('Y-m-d H:i:s',strtotime("$start_date + $coupon_day day"));

                        $conn->insert('user_coupon', $data);

                        $coupon_id = $conn->lastInsertId();
                        $this->sendCouponWX($coupon_id, 1);

                        $count ++;
                    }
                }

                $result = array('flag' => 0, 'msg' => '充值成功');
            }

        }
        return new JsonResponse($result);
    }

    /**
     * 消费记录
     */
    public function consumptionAction()
    {
        $user_id =$this->getSession('user_id');
        if (empty($user_id)) {
            return $this->redirect($this->generateUrl('user_index'));
        }
        return $this->render('UserBundle:Personal:consumption.html.twig');
    }

    /**
     * 消费记录 加载更多
     *
     * @param
     */
    public function consumptionMoreAction()
    {
        $user_id =$this->getSession('user_id');
        $result = array('flag' => 0, 'msg' => '');
        if (empty($user_id)) {
            $result = array('flag' => 1, 'msg' => '请先登录');
        }

        $request = $this->get('request');
        $conn = $this->get('database_connection');

        $limit = 20;
        $page = (int)$request->query->get('page') >0 ? (int)$request->query->get('page'): 1;
        $start = ($page - 1) * $limit;

        $consumption = $conn->fetchAll("SELECT uc.id, uc.storesOwned, s.name AS store_name, uc.amount, uc.coupon_id, c.name AS coupon_name, uc.add_date, uc.mark FROM user_consumption uc LEFT JOIN store s ON s.id = uc.storesOwned LEFT JOIN coupon c ON c.id = uc.coupon_id WHERE uc.user_id = ? AND uc.amount>0 ORDER BY uc.add_date DESC, id DESC LIMIT $start, $limit", array($user_id));
        $result = array('flag' => 0, 'msg' => $consumption);

        return new JsonResponse($result);
    }

    /**
     * 我的积分
     */
    public function pointsAction()
    {
        $user_id =$this->getSession('user_id');
//        $user_id =5;
        if (empty($user_id)) {
            return $this->redirect($this->generateUrl('user_index'));
        }
        $conn = $this->get('database_connection');
        $points = $conn->fetchColumn("SELECT points FROM user WHERE id=?", array($user_id));
        return $this->render('UserBundle:Personal:points.html.twig', array('points' => $points));
    }

    /**
     * 会员等级说明
     */
   public function vipAction()
   {
       $user_id =$this->getSession('user_id');
       if (empty($user_id)) {
           return $this->redirect($this->generateUrl('user_index'));
       }
       $conn = $this->get('database_connection');

       $vip_rank = $conn->fetchAssoc("SELECT name, value FROM article WHERE id = 1 ");

       return $this->render('UserBundle:Personal:vip.html.twig', array('vip' => $vip_rank));
   }

    /**
     * 升级会员
     */
    public function promotionVipAction()
    {
        $result = array('flag' => 0, 'msg' => '');
        $user_id =$this->getSession('user_id');
        if (empty($user_id)) {
            $result = array('flag' => 1, 'msg' => '请先领取会员卡');
        } else {
            $request = $this->get('request');
            $conn = $this->get('database_connection');

            $vip_points = trim($request->request->get('vip_points'));
//            $vip_points = 30000;

            $user = $conn->fetchAssoc("SELECT rank, points FROM user WHERE id =?", array($user_id));

            if ($vip_points < 5000 ) {
                $result = array('flag' => 2, 'msg' => '您的积分不够，暂时不能升级会员');
            } elseif ($user['rank'] == 4) {
                $result = array('flag' => 3, 'msg' => '您已升级到顶级会员');
            } else {
                $rank = (int)$user['rank'] + 1;
                $points = (int)$user['points'] - 5000;

                // 更新等级和积分
                $flag_poins = $conn->update("user", array('rank'=>$rank, 'points' => $points), array('id' => $user_id));
                // 插入积分记录表
                $flag_consumption = $conn->insert("user_consumption", array('mark'=>'用户升级会员', 'point_type' => 2, 'point_limit' => 5000, 'add_date' => date('Y-m-d H:i:s')), array('id' => $user_id));
                if ($flag_poins && $flag_consumption) {
                    $result = array('flag' => 0, 'msg' => '升级成功');
                }

            }
        }
        return new JsonResponse($result);
    }

    /**
     * 我的积分 加载更多
     *
     * @param
     */
    public function pointsMoreAction()
    {
        $user_id =$this->getSession('user_id');
//        $user_id =5;
        if (empty($user_id)) {
            $result = array('flag' => 1, 'msg' => '请先领取会员卡');
        }

        $request = $this->get('request');
        $conn = $this->get('database_connection');

        $limit = 20;
        $page = (int)$request->query->get('page') >0 ? (int)$request->query->get('page'): 1;
        $start = ($page - 1) * $limit;

        $points = $conn->fetchAll("SELECT uc.id, uc.point_type, uc.point_limit, uc.add_date, uc.storesOwned, s.name AS store_name FROM user_consumption uc LEFT JOIN store s ON s.id = uc.storesOwned WHERE uc.user_id = ? ORDER BY uc.add_date DESC, id DESC LIMIT $start, $limit", array($user_id));
        $result = array('flag' => 0, 'msg' => $points);

        return new JsonResponse($result);
    }

    /**
     * 我的优惠券
     */
    public function couponAction()
    {
        $user_id =$this->getSession('user_id');
        if (empty($user_id)) {
            return $this->redirect($this->generateUrl('user_index'));
        }

        return $this->render('UserBundle:Personal:coupon.html.twig');
    }

    /**
     * 我的优惠券 加载更多
     *
     * @param $type  $type:1 待使用 2 已失效
     */
    public function couponMoreAction()
    {
        $result = array('flag' => 0, 'msg' => '');
        $user_id =$this->getSession('user_id');
        if (empty($user_id)) {
            $result = array('flag' => 1, 'msg' => '请先登录');
        } else {
            $request = $this->get('request');
            $conn = $this->get('database_connection');

            $limit = 20;
            $page = (int)$request->query->get('page') >0 ? (int)$request->query->get('page'): 1;
            $start = ($page - 1) * $limit;

            $type = $request->query->get('type');
//        $type = 1;

            if ($type == 1) {
                $coupon = $conn->fetchAll("SELECT uc.id, c.type, uc.coupon_number, c.name AS coupon_name, c.amount, uc.end_date FROM user_coupon uc LEFT JOIN  coupon c ON c.id = uc.coupon_id WHERE uc.is_used = 0 AND uc.end_date >= NOW() AND uc.user_id = ? ORDER BY uc.end_date DESC, id DESC LIMIT $start, $limit", array($user_id));
            } else {
                $coupon_used = $conn->fetchAll("SELECT uc.id, c.type, uc.coupon_number, c.name AS coupon_name, c.amount, uc.end_date, uc.is_used FROM user_coupon uc LEFT JOIN  coupon c ON c.id = uc.coupon_id WHERE uc.is_used = 1 AND uc.user_id = ? ORDER BY uc.end_date DESC, id DESC", array($user_id));
                $coupon_date = $conn->fetchAll("SELECT uc.id, c.type, uc.coupon_number, c.name AS coupon_name, c.amount, uc.end_date, uc.is_used FROM user_coupon uc LEFT JOIN  coupon c ON c.id = uc.coupon_id WHERE uc.is_used = 0 AND uc.end_date < NOW() AND uc.user_id = ? ORDER BY uc.end_date DESC, id DESC ", array($user_id));
                $coupon = array_merge($coupon_used, $coupon_date);
                $coupon=array_slice($coupon,$start,$limit);
            }
            $result = array('flag' => 0, 'msg' => $coupon);
        }

        return new JsonResponse($result);
    }

    /**
     * 适用门店
     */
    public function storeAction()
    {
        $user_id =$this->getSession('user_id');
//        $user_id =1;
        if (empty($user_id)) {
            return $this->redirect($this->generateUrl('user_index'));
        } else {
            $request = $this->get('request');
            $conn = $this->get('database_connection');

            $store = $conn->fetchAll("SELECT * FROM store");
        }
        return $this->render('UserBundle:Personal:store.html.twig', array('store' => $store));
    }

    /**
     * 期待和意见
     */
    public function feedbackAction()
    {
        $user_id =$this->getSession('user_id');
        if (empty($user_id)) {
            return $this->redirect($this->generateUrl('user_index'));
        } else {
            $request = $this->get('request');
            $conn = $this->get('database_connection');

            if ($request->getMethod() == 'POST') {
//                $data['type'] = trim($request->request->get('type'));
                $data['value'] =  $this->filterExpression($request->request->get('value'));
                $data['add_date'] = date('Y-m-d H:i:s');
                $data['user_id'] = $user_id;

                $conn->insert('feedback', $data);
                return $this->redirect($this->generateUrl('user_personal'));
            }
        }

        return $this->render('UserBundle:Personal:feedback.html.twig');
    }

    /**
     * 个人信息
     */
    public function infoAction()
    {
        $user_id =$this->getSession('user_id');
//        $user_id =5;
        if (empty($user_id)) {
            return $this->redirect($this->generateUrl('user_index'));
        }

        $conn = $this->get('database_connection');

        $info = $conn->fetchAssoc("SELECT avatar, avatar_update, nickname, name, sex, phone, birthday, qq, mail, change_birthday FROM user WHERE id = ?", array($user_id));

        $birthday = $conn->fetchColumn("SELECT value FROM config WHERE name = 'birthday'");

        if ($info['change_birthday'] < (int)$birthday) { // 用户设置的生日次数没有达到后台设置的次数
            $birthday = 0;
        } else {
            $birthday = 1;
        }
        return $this->render('UserBundle:Personal:info.html.twig', array('info' => $info, 'birthday' => $birthday));
    }

    /**
     * 个人信息 修改
     *
     * @param $type 信息修改类型 type:1:头像 2：昵称 3：姓名 4:性别 5:手机号 6:生日 7:qq号 8:邮箱
     */
    public function changeInfoAction()
    {
        $result = array('flag' => 0, 'msg' => '');
        $user_id =$this->getSession('user_id');
//        $user_id =5;
        if (empty($user_id)) {
            $result = array('flag' => 1, 'msg' => '请先登录');
            return new JsonResponse($result);
        }

        $request = $this->get('request');
        $conn = $this->get('database_connection');

        $type = $request->query->get('type');

        $data = array();

        if (empty($type)) {
            $result = array('flag' => 2, 'msg' => '修改失败');
            return new JsonResponse($result);
        } elseif ($type == 1) {
            $data['avatar_update'] = $this->filterExpression($request->request->get('avatar_update'));
            $flag = $conn->update('user', $data, array('id' => $user_id));
            $result = array('flag' => 0, 'msg' => '修改成功');
            return new JsonResponse($result);

        } elseif ($type == 2) {
            $data['nickname'] = $this->filterExpression($request->request->get('nickname'));
        } elseif ($type == 3) {
            $data['name'] = $this->filterExpression($request->request->get('name'));
        } elseif ($type == 4) {
            $data['sex'] = trim($request->request->get('sex'));
        } elseif ($type == 5) {
            $data['phone'] = trim($request->request->get('phone'));

            //验证手机号是否已被注册
            $is_phone = $conn->fetchColumn("SELECT id FROM user WHERE phone =?", array($data['phone']));
            if ($is_phone) {
                $result = array('flag' => 4, 'msg' => '该手机号已被使用');
                return new JsonResponse($result);
            }
        } elseif ($type == 6) {
            $data['birthday'] = trim($request->request->get('birthday'));
            $change_birthday = $conn->fetchColumn("SELECT change_birthday FROM user WHERE id =?", array($user_id));
        } elseif ($type == 7) {
            $data['qq'] = $this->filterExpression($request->request->get('qq'));
        } elseif ($type == 8) {
            $data['mail'] = trim($request->request->get('mail'));
        }


        if (empty($data)) {
            $flag = 0;
        } else {
            $flag = $conn->update('user', $data, array('id' => $user_id));
            if ($type == 6) {
                $flag = $conn->update('user', array('change_birthday' => $change_birthday+1), array('id' => $user_id));
            }
        }

        if ($flag) {
            $result = array('flag' => 0, 'msg' => '修改成功');
        } else {
            $result = array('flag' => 3, 'msg' => '修改失败');
        }

        return new JsonResponse($result);
    }

    /**
     * 使用帮助
     */
    public function helpListAction()
    {
        $user_id =$this->getSession('user_id');
        if (empty($user_id)) {
            return $this->redirect($this->generateUrl('user_index'));
        }

        return $this->render('UserBundle:Personal:help_list.html.twig');
    }

    /**
     * 使用帮助 加载更多
     */
    public function helpListMoreAction()
    {
        $request = $this->get('request');
        $conn = $this->get('database_connection');

        $limit = 20;
        $page = (int)$request->query->get('page') >0 ? (int)$request->query->get('page'): 1;
        $start = ($page - 1) * $limit;

        $help = $conn->fetchAll("SELECT id, name FROM article WHERE id > 2 ORDER BY sort_order DESC, id DESC LIMIT $start, $limit");

        return new JsonResponse($help);
    }

    /**
     * 使用帮助 阅读页
     *
     * @param $id 使用帮助文章的id
     */
    public function helpShowAction($id)
    {
        $user_id =$this->getSession('user_id');
//        $user_id =5;
        if (empty($user_id)) {
            return $this->redirect($this->generateUrl('user_index'));
        }

        $conn = $this->get('database_connection');

        $show = $conn->fetchAssoc("SELECT name, value FROM article WHERE id = ?", array($id));

        return $this->render('UserBundle:Personal:help_show.html.twig', array('show' => $show, 'id'=>$id));
    }

}
