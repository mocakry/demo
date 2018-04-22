<?php

namespace AdminBundle\Controller;

use BaseBundle\Controller\BaseController;
use BaseBundle\Controller\Pagination;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class StoreController extends BaseController
{
    /**
     * 门店信息
     */
    public function indexAction()
    {
        $admin_id =$this->getSession('admin_id');
        if(empty($admin_id)){
            return $this->redirect($this->generateUrl('admin_login'));
        }

        $request = $this->get('request');
        $conn = $this->get('database_connection');

        $store = $conn->fetchAll("SELECT * FROM store");

        //门店下有管理员
        $store_arr = $conn->fetchAll("SELECT storesOwned FROM admin GROUP BY storesOwned");
        $store_has = array();
        foreach($store_arr as $k => $v){
            $store_has[] = $v['storesOwned'];
        }

        foreach ($store as $k => $v) {
            if (in_array($v['id'], $store_has)) { // 门店下有管理员
                $store[$k]['is_has'] = 1;
            } else {// 门店下无管理员
                $store[$k]['is_has'] = 0;
            }
        }

        return $this->render('AdminBundle:Store:index.html.twig', array('store' => $store));
    }

    /**
     * 门店信息 添加或编辑
     *
     * @param $id 文章的id
     */
    public function storeEditAction($id)
    {
        $error = array('content' => '');
        $admin_id =$this->getSession('admin_id');
        if (empty($admin_id)) {
            return $this->redirect($this->generateUrl('admin_login'));
        } else {
            $request = $this->get('request');
            $conn = $this->get('database_connection');

            if ($request->getMethod() == 'POST') {

                $data['name'] = trim($request->request->get('name'));
                $data['address'] = trim($request->request->get('address'));
                $data['time'] = trim($request->request->get('time'));
                $data['phone'] = trim($request->request->get('phone'));

                if (isset($_FILES['photo']) && $_FILES['photo']['name']) {
                    $data['photo'] = $this->fileUpload('photo', '', 'upload/avatar_update/');
                }

                if ($id) {
                    $conn->update('store', $data, array('id' => $id));
                } else {
                    $conn->insert('store', $data);
                }
                return $this->redirect($this->generateUrl('admin_all_store'));
            }

            if($id){
                $store = $conn->fetchAssoc("SELECT * FROM store WHERE id =?", array($id));
            } else {
                $store = array();
            }
        }
        return $this->render('AdminBundle:Store:edit.html.twig', array('id' => $id, 'store' => $store, 'error' => $error));
    }

    /**
     * 门店信息 删除
     *
     * @param $id 门店信息id
     */
    public function delAction($id)
    {
        $admin_id =$this->getSession('admin_id');
        if (empty($admin_id)) {
            return $this->redirect($this->generateUrl('admin_login'));
        } else {
            $request = $this->get('request');
            $conn = $this->get('database_connection');

            $conn->delete('store', array('id' => $id));
        }
        return $this->redirect($this->generateUrl('admin_all_store'));
    }
}
