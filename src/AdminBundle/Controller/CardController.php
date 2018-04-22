<?php

namespace AdminBundle\Controller;

use BaseBundle\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CardController extends BaseController
{
    /**
     * 实体卡
     */
    public function indexAction()
    {
        $admin_id =$this->getSession('admin_id');
        if (empty($admin_id)) {
            return $this->redirect($this->generateUrl('admin_login'));
        } else {
            $request = $this->get('request');
            $conn = $this->get('database_connection');

            $card = $conn->fetchAll("SELECT id, card_id, name, rank, number, COUNT(coupon_id) AS coupon , SUM(coupon_number) AS coupon_number FROM offline_card GROUP BY card_id ORDER BY id DESC");

        }
        return $this->render('AdminBundle:Card:index.html.twig', array('card' => $card));
    }

    /**
     * 实体卡 添加或编辑
     *
     * @param $id 实体卡的id
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
                $name = trim($request->request->get('name'));
                $rank = trim($request->request->get('rank'));
                $number = trim($request->request->get('number'));
                $card_id = $this->getCardsNumber($admin_id);
                $coupon_arr = trim($request->request->get('coupon_arr'));
                $coupon = explode(',', $coupon_arr);
                foreach ($coupon as $k => $v ) { //$data是二维数组
                    $data[$k]['name'] = $name;
                    $data[$k]['rank'] = $rank;
                    $data[$k]['number'] = $number;
                    $data[$k]['card_id'] = $card_id;
                    $data[$k]['coupon_id'] = $v[0];
                    $data[$k]['coupon_number'] = $v[2];
                }
                foreach ($data as $value) {
                    $conn->insert('offline_card', $value);
                }

                // 生成子卡
                $sub_card = $this->getCardNumber($admin_id, $number);
                $conn = $this->get('database_connection');

                foreach ($sub_card as $k => $v ) {
                    $conn->insert("sub_card",array('card_id' => $card_id, 'Sub_card_id' => $v, 'is_used' => 0));
                }
                return $this->redirect($this->generateUrl('admin_card'));
            }

            $coupon_all = $conn->fetchAll("SELECT * FROM coupon ORDER BY id DESC");
        }

        return $this->render('AdminBundle:Card:edit.html.twig', array('id'=>$id, 'coupon' => $coupon_all));
    }

    /**
     * 实体卡 的 子卡列表
     *
     * @param $id 实体卡的编号
     */
    public function subCardAction($id)
    {
        $admin_id =$this->getSession('admin_id');
        if (empty($admin_id)) {
            return $this->redirect($this->generateUrl('admin_login'));
        } else {
            $request = $this->get('request');
            $conn = $this->get('database_connection');

            $coupon = $conn->fetchAll("SELECT oc.id, c.name AS coupon_name, oc.coupon_number FROM offline_card oc LEFT JOIN coupon c ON c.id = oc.coupon_id WHERE oc.card_id = ?", array($id));

            $sub_card = $conn->fetchAll("SELECT id, Sub_card_id, is_used FROM sub_card WHERE card_id = ? ", array($id));
        }
        return $this->render('AdminBundle:Card:sub_card.html.twig', array('id' => $id, 'coupon' => $coupon, 'sub_card' => $sub_card));
    }

    /**
     * 导出子卡
     */
    public function exportAction()
    {
        $request = $this->get('request');
        $conn = $this->get('database_connection');

        if ($request->query->has('id') && $request->query->get('id')) {
            $id = $request->query->get('id');
        }

        $sub_card = $conn->fetchAll("SELECT id, Sub_card_id FROM sub_card WHERE card_id = ? ", array($id));

        $objExcel = new \PHPExcel();
        $objWriter = new \PHPExcel_Writer_Excel5($objExcel);
        $objExcel->setActiveSheetIndex();
        $objActSheet = $objExcel->getSheet();
        $objActSheet->setTitle('子卡列表');

        $objActSheet->getColumnDimension('A')->setWidth('14');
        $objActSheet->getColumnDimension('B')->setWidth('20');

        $objActSheet->setCellValue('A1', '序号');
        $objActSheet->setCellValue('B1', '子卡编号');

        $i = 2;
        foreach ($sub_card as $item) {
            $objActSheet->setCellValue('A' . $i, $i-1);
            $objActSheet->setCellValue('B' . $i, $item['Sub_card_id']);
            $i++;
        }

        $outputFileName = '子卡列表.xls';
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
//            header('Content-Disposition:attachment;filename="' . $outputFileName . '"');  //到文件
        header('Content-Disposition:inline;filename="'.$outputFileName.'"');  //到浏览器
        header("Content-Transfer-Encoding: binary");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');

        return new Response();
    }
}
