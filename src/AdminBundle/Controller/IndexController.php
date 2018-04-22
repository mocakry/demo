<?php

namespace AdminBundle\Controller;

use BaseBundle\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IndexController extends BaseController
{
    /**
     * 登录
     */
    public function loginAction()
    {
        $request = $this->get('request');
        $conn = $this->get('database_connection');
        $admin_id =$this->getSession('admin_id');

        $error = array('content' => '', 'account' => '');
        if ($admin_id) {
            return $this->redirect($this->generateUrl('admin_index'));
        } else {
            if ($request->getMethod()=='POST') {
                $account = trim($request->request->get('account'));
                $password = sha1(md5($request->request->get('password')));

                $admin = $conn->fetchAssoc("SELECT * FROM admin WHERE account=? AND password =?", array($account, $password));
                if ($admin) {
                    $data = array(
                        'admin_id' => $admin['id'],
                        'account' => $admin['account'],
                        'power' => $admin['storesOwned']
                    );
                    $this->setSession($data);
                    $redirctUrl = $request->query->get('currentUrl');

                    if (empty($redirctUrl)) {
                        return $this->redirect($this->generateUrl('admin_index'));
                    } else {
                        return $this->redirect($redirctUrl);
                    }

                } else {
                    $error = array(
                        'content' => '您输入的帐号/密码有误',
                        'account' => $account
                    );
                }
            }
        }
        return $this->render('AdminBundle:Index:login.html.twig', array('error' => $error));
    }

    /**
     * 忘记密码
     */
    public function forgetPasswordAction()
    {
        $request = $this->get('request');
        $conn = $this->get('database_connection');
        $admin_id =$this->getSession('admin_id');
//
        $error = array('content' => '', 'account' => '');
        if ($admin_id) {
            return $this->redirect($this->generateUrl('admin_index'));
        } else {
            if ($request->getMethod()=='POST') {
                $mail = trim($request->request->get('mail'));

                $admin = $conn->fetchAssoc("SELECT * FROM admin WHERE account='admin'");

                if ($mail == $admin['admin_mail']) {
                    $password = 'yaoye'.rand(100000,999999);
                    $new_password = sha1(md5($password));
                    $conn -> update('admin', array('password' => $new_password), array('id' => 1));
                    $this->sendmail($password);
                    return $this->redirect($this->generateUrl('admin_login'));
                } else {
                    $error = array(
                        'content' => '邮箱错误',
                        'account' => $mail
                    );
                }
            }
        }
        return $this->render('AdminBundle:Index:forget.html.twig', array('error' => $error));
    }

    /**
     * 退出
     */
    public function logoutAction()
    {
        $this->clearSession();
        return $this->redirect($this->generateUrl('admin_login'));
    }

    /**
     * 欢迎页
     */
    public function indexAction()
    {
        $admin_id = $this->getSession('admin_id');
        if (empty($admin_id)) {
            return $this->redirect($this->generateUrl('admin_login'));
        }
        
        return $this->render('AdminBundle:Index:index.html.twig');
    }

    /**
     * 数据与统计
     */
    public function statisticsAction()
    {
        $admin_id = $this->getSession('admin_id');
        if (empty($admin_id)) {
            return $this->redirect($this->generateUrl('admin_login'));
        }

        $request = $this->get('request');
        $conn = $this->get('database_connection');

        $where = "WHERE 1";
        //开始时间
        if ($request->query->has('start_date') && $request->query->get('start_date')) {
            $filter['start_date'] = $request->query->get('start_date');
            $where  .= " AND uc.add_date > '" . $filter['start_date'] . " 00:00:00'";
        } else {
            $filter['start_date'] = "";
        }
        //截止时间
        if ($request->query->has('end_date') && $request->query->get('end_date')) {
            $filter['end_date'] = $request->query->get('end_date');
            $where  .= " AND uc.add_date < '" . $filter['end_date'] . " 23:59:59'";
        } else {
            $filter['end_date'] = "";
        }

        $points = $conn->fetchAll("SELECT uc.storesOwned, uc.point_type, uc.point_limit, uc.coupon_id, s.name AS store_name FROM user_consumption uc LEFT JOIN store s ON s.id = uc.storesOwned $where ORDER BY uc.add_date DESC ");
        $res = array(); //想要的结果
        foreach ($points as $k => $v) {
            if (!isset($res[$v['storesOwned']]['add_points'])) {
                $res[$v['storesOwned']]['add_points'] = 0;
                $res[$v['storesOwned']]['reduce_points'] = 0;
                $res[$v['storesOwned']]['coupon_count'] = 0;
            }

            $res[$v['storesOwned']]['storesOwned'] = $v['storesOwned'];
            $res[$v['storesOwned']]['store_name'] = $v['store_name'];

            if ($v['point_type'] == 1) {
                $res[$v['storesOwned']]['add_points'] += $v['point_limit'];
            } else {
                $res[$v['storesOwned']]['reduce_points'] += $v['point_limit'];
            }

            if ($v['coupon_id'] > 0) {
                $res[$v['storesOwned']]['coupon_count'] += 1;
            } else {
                $res[$v['storesOwned']]['coupon_count'] = $res[$v['storesOwned']]['coupon_count'];
            }
            
        }
        
        return $this->render('AdminBundle:Index:statistics.html.twig', array('data' => $res, 'filter' => $filter));
    }

    /**
     * 后台设置可以设置生日次数
     */
    public function birthdayAction()
    {
        $admin_id = $this->getSession('admin_id');
        if (empty($admin_id)) {
            return $this->redirect($this->generateUrl('admin_login'));
        }

        $request = $this->get('request');
        $conn = $this->get('database_connection');

        if ($request->getMethod() == "POST") {
            $value = trim($request->request->get('value'));

            $conn->update('config', array('value'=>$value), array('name' => 'birthday'));
        }

        $birthday = $conn->fetchColumn("SELECT value FROM config WHERE name = 'birthday'");

        return $this->render('AdminBundle:Index:birthday.html.twig', array('birthday' => $birthday));
    }
}
