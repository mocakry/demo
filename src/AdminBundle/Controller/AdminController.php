<?php

namespace AdminBundle\Controller;

use BaseBundle\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdminController extends BaseController
{
    /**
     * 管理员管理
     */
    public function indexAction()
    {
        $admin_id =$this->getSession('admin_id');
        if (empty($admin_id)) {
            return $this->redirect($this->generateUrl('admin_login'));
        } else {
            $request = $this->get('request');
            $conn = $this->get('database_connection');

            $admin = $conn->fetchAll("SELECT a.id, a.account, a.storesOwned, s.name AS store_name FROM admin a LEFT JOIN store s ON s.id = a.storesOwned");
        }
        return $this->render('AdminBundle:Admin:index.html.twig', array('admin' => $admin));
    }

    /**
     * 管理员管理 添加或编辑
     *
     * @param $id 管理员的id
     */
    public function editAction($id)
    {
        $error = array('content' => '', 'account' => '');
        $admin_id =$this->getSession('admin_id');
        if (empty($admin_id)) {
            return $this->redirect($this->generateUrl('admin_login'));
        } else {
            $request = $this->get('request');
            $conn = $this->get('database_connection');

            if ($request->getMethod() == 'POST') {
                $data['account'] = trim($request->request->get('account'));
                $data['storesOwned'] = trim($request->request->get('storesOwned'));
                $data['admin_mail'] = '';
                if ($request->request->get('password')){
                    $data['password'] = sha1(md5($request->request->get('password')));
                }

                $is_admin = $conn->fetchColumn("SELECT id FROM admin WHERE account =? ", array($data['account']));


                if ($id) {
                    $conn->update('admin', $data, array('id' => $id));
                    return $this->redirect($this->generateUrl('admin_admin'));
                } else {
                    if ($is_admin) {
                        $error = array('content' => '该帐号已存在', 'account' => $data['account']);
                    } else {
                        $conn->insert('admin', $data);
                        return $this->redirect($this->generateUrl('admin_admin'));
                    }
                }
            }
            if($id){
                $admin = $conn->fetchAssoc("SELECT * FROM admin WHERE id =?", array($id));
            } else {
                $admin = array();
            }
            $store = $conn->fetchAll("SELECT * FROM store");
        }
        return $this->render('AdminBundle:Admin:edit.html.twig', array('admin' => $admin, 'store' => $store, 'id' => $id, 'error' => $error));
    }

    /**
     * 管理员管理 删除
     *
     * @param $id 管理员的id
     */
    public function delAction($id)
    {
        $admin_id =$this->getSession('admin_id');
        if (empty($admin_id)) {
            return $this->redirect($this->generateUrl('admin_login'));
        } else {
            $request = $this->get('request');
            $conn = $this->get('database_connection');

            $conn->delete('admin', array('id' => $id));
        }
        return $this->redirect($this->generateUrl('admin_admin'));
    }


    /**
     * 管理员管理 修改密码
     *
     */
    public function passwordAction()
    {
        $error = array('content' => '');
        $admin_id =$this->getSession('admin_id');
        if (empty($admin_id)) {
            return $this->redirect($this->generateUrl('admin_login'));
        } else {
            $request = $this->get('request');
            $conn = $this->get('database_connection');

            if ($request->getMethod() == 'POST') {
                $old_password = sha1(md5($request->request->get('old_password')));
                $password = sha1(md5($request->request->get('password')));
                $password_two = sha1(md5($request->request->get('password_two')));

                $is_password = $conn->fetchColumn("SELECT id FROM admin WHERE id=? AND password=?", array($admin_id, $old_password));

               if (empty($is_password)) {
                   $error = array('content' => '原密码不正确');
               } elseif ($password != $password_two) {
                   $error = array('content' => '两次密码不一致');
               } else {
                   $conn->update('admin', array('password' => $password), array('id' => $admin_id));
               }

            }
        }
        return $this->render('AdminBundle:Admin:password.html.twig', array('error' => $error));
    }
}
