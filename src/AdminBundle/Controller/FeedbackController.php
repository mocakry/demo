<?php

namespace AdminBundle\Controller;

use BaseBundle\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FeedbackController extends BaseController
{
    /**
     * 期待与反馈
     */
    public function indexAction()
    {
        $admin_id =$this->getSession('admin_id');
        if (empty($admin_id)) {
            return $this->redirect($this->generateUrl('admin_login'));
        } else {
            $request = $this->get('request');
            $conn = $this->get('database_connection');

            $feedback = $conn->fetchAll("SELECT * FROM feedback ORDER BY add_date DESC");
        }
        return $this->render('AdminBundle:Feedback:index.html.twig', array('feedback' => $feedback));
    }
}
