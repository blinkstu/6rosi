<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Arys
// +----------------------------------------------------------------------
namespace app\main\controller;

use app\main\model\OrdersModel;
use app\main\model\OrdersSiModel;
use think\Db;
use cmf\controller\AdminBaseController;

class AdminOrdersController extends AdminBaseController
{
    public function index()
    {
        $orders = new OrdersModel();
        $orders = $orders->with('user')->with('item')->order('id desc')->select();

        $this->assign('orders', $orders);
        return $this->fetch();
    }

    public function deliveryPost()
    {
        if ($this->request->isPost()) {
            $params = $this->request->param();
            if (empty($params['id'])) {
                return $this->error('缺少参数');
            }
            $id = $params['id'];
            
            if (empty($params['delivery'])) {
                $delivery = '';
            }else{
                $delivery = $params['delivery'];
            }
           
            

            Db::name('orders')->where('id', $id)->update(['delivery' => $delivery]);

            return $this->success('保存成功');
        }
    }

    public function deliverysiPost()
    {
        if ($this->request->isPost()) {
            $params = $this->request->param();
            if (empty($params['id'])) {
                return $this->error('缺少参数');
            }
            $id = $params['id'];
            
            if (empty($params['delivery'])) {
                $delivery = '';
            }else{
                $delivery = $params['delivery'];
            }
           
            

            Db::name('orders_si')->where('id', $id)->update(['delivery' => $delivery]);

            return $this->success('保存成功');
        }
    }

    public function sishu()
    {
        $orders = new OrdersSiModel();
        $orders = $orders->with('user')->with('album')->order('id desc')->select();

        $this->assign('orders', $orders);
        return $this->fetch();
    }

}
