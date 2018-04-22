<?php

namespace AdminBundle\Controller;

use BaseBundle\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ArticleController extends BaseController
{
    /**
     * 文章管理
     */
    public function indexAction()
    {
        $admin_id =$this->getSession('admin_id');
        if (empty($admin_id)) {
            return $this->redirect($this->generateUrl('admin_login'));
        } else {
            $request = $this->get('request');
            $conn = $this->get('database_connection');

            $where = "WHERE 1";

            if ($request->query->has('name') && $request->query->get('name')) {
                $filter['name'] = $request->query->get('name');
                $where .= " AND a.name LIKE '%". $filter['name'] ."%' ";
            } else {
                $filter['name'] = "";
            }

            $article = $conn->fetchAll("SELECT a.id, a.name FROM article a $where ORDER BY a.sort_order DESC, a.id DESC");

        }
        return $this->render('AdminBundle:Article:index.html.twig', array('article' => $article, 'filter' => $filter));
    }

    /**
     * 文章管理 添加或编辑
     *
     * @param $id 文章的id
     */
    public function editAction($id)
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
                $data['type'] = trim($request->request->get('type'));
                $data['value'] = trim($request->request->get('value'));
                $data['sort_order'] = trim($request->request->get('sort_order'));
                if (empty($data['value'])) {
                    $error = array('content' => '文章内容不能为空');
                }else {
                    if (empty($data['sort_order'])) {
                        $data['sort_order'] = 0;
                    }

                    if ($id) {
                        $conn->update('article', $data, array('id' => $id));
                    } else {
                        $conn->insert('article', $data);
                    }
                    return $this->redirect($this->generateUrl('admin_article'));
                }
            }
            if($id){
                $articles = $conn->fetchAssoc("SELECT * FROM article WHERE id =?", array($id));
            } else {
                $articles = array();
            }
        }
        return $this->render('AdminBundle:Article:edit.html.twig', array('id' => $id, 'articles' => $articles, 'error' => $error));
    }

    /**
     * 文章管理 删除
     *
     * @param $id 文章的id
     */
    public function delAction($id)
    {
        $admin_id =$this->getSession('admin_id');
        if (empty($admin_id)) {
            return $this->redirect($this->generateUrl('admin_login'));
        } else {
            $request = $this->get('request');
            $conn = $this->get('database_connection');
            $conn->delete('article', array('id' => $id));
        }
        return $this->redirect($this->generateUrl('admin_article'));
    }
}
