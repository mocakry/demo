<?php

namespace AdminBundle\Controller;

use BaseBundle\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SiteController extends BaseController
{
    /**
     * 超级管理员 添加
     */
    public function adminInsertAction()
    {
        $request = $this->get('request');
        $conn = $this->get('database_connection');

        $is_add = $conn->fetchColumn("SELECT id FROM admin WHERE account = 'admin'");
        $password = sha1(md5(123456));

        if ($is_add) {
            $conn -> update('admin', array('account' => 'admin', 'password' => $password, 'admin_mail' => 'zhouyou0101@163.com','storesOwned' => 0 ), array('id' => $is_add));
        } else {
            $conn -> insert('admin', array('account' => 'admin', 'password' => $password, 'admin_mail' => 'zhouyou0101@163.com', 'storesOwned' => 0 ));
        }

        return $this->redirect($this->generateUrl('admin_login'));
    }

    /**
     * 会员等级说明 添加
     */
    public function vipRankInsertAction()
    {
        $request = $this->get('request');
        $conn = $this->get('database_connection');

        $is_add = $conn->fetchColumn("SELECT id FROM article WHERE name = '会员等级说明'");
        if (empty($is_add)) {
            $conn -> insert('article', array('name' => '会员等级说明', 'type' => 1, 'value' => '会员等级说明', 'sort_order' => 1 ));
        }
        return $this->redirect($this->generateUrl('admin_article'));
    }

    /**
     * 积分兑换商城 添加
     */
    public function siteVipPointsAction()
    {
        $request = $this->get('request');
        $conn = $this->get('database_connection');

        $is_add = $conn->fetchColumn("SELECT id FROM article WHERE name = '积分兑换商城'");
        if (empty($is_add)) {
            $conn -> insert('article', array('name' => '积分兑换商城', 'type' => 1, 'value' => '积分兑换商城', 'sort_order' => 1 ));
        }
        return $this->redirect($this->generateUrl('admin_article'));
    }

    /**
     * 系统内置优惠券 三条 添加
     */
    public function siteCouponAction()
    {
        $request = $this->get('request');
        $conn = $this->get('database_connection');

        $is_add_arr = $conn->fetchAll("SELECT id FROM coupon ");
        $is_add = array();
        foreach ($is_add_arr as $k => $v) {
            $is_add[$v['id']] = $v['id'];
        }

        if (!in_array(1, $is_add) && !in_array(2, $is_add) && !in_array(3, $is_add)) {
            for ($i=1; $i<=3; $i++) {
                $amount = (int)$i+'8.00';
                $conn -> insert('coupon', array('id' => $i, 'name' => '系统赠送优惠券'.$i, 'amount' => $amount, 'day' => '10', 'type' => 1 ));
            }
        } else if (in_array(1, $is_add) || in_array(2, $is_add) || in_array(3, $is_add)) {

            $conn -> update('coupon', array('id' => 1, 'name' => '买一送一', 'amount' => '0.00', 'day' => '15', 'type' => 2 ), array('id' => 1));
            $conn -> update('coupon', array('id' => 2, 'name' => '生日免费券', 'amount' => '0.00', 'day' => '730', 'type' => 2 ), array('id' => 2));
            $conn -> update('coupon', array('id' => 3, 'name' => '2元优惠券', 'amount' => '2.00', 'day' => '1', 'type' => 1 ), array('id' =>3));

        }

        return $this->redirect($this->generateUrl('admin_coupon'));
    }

    /**
     * 系统内置添加生日修改次数 添加
     */
    public function siteBirthdayAction()
    {
        $request = $this->get('request');
        $conn = $this->get('database_connection');

        $is_add = $conn->fetchColumn("SELECT id FROM config WHERE name = 'birthday'");
        if (empty($is_add)) {
            $conn -> insert('config', array('name' => 'birthday', 'value' => 1 ));
        }
        return $this->redirect($this->generateUrl('admin_birthday'));
    }

}
