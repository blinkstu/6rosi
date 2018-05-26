<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Powerless < wzxaini9@gmail.com>
// +----------------------------------------------------------------------
namespace app\user\controller;

use cmf\lib\Storage;
use think\Validate;
use think\Image;
use cmf\controller\UserBaseController;
use app\user\model\UserModel;
use app\user\model\BuyLogModel;
use app\user\model\OrdersSiModel;
use app\user\model\OrdersModel;
use think\Db;

class MemberController extends UserBaseController
{

    function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 会员中心首页
     */
    public function index()
    {
        $user = cmf_get_current_user();
        $this->assign($user);

        $userId = cmf_get_current_user_id();

        $userModel = new UserModel();
        $data      = $userModel->where('id', $userId)->find();
        $this->assign('data', $data);
        return $this->fetch(':member');
    }


    public function coins(){
        return $this->fetch(':coins');
    }

    public function operations(){
        $user = cmf_get_current_user();

        $data = Db::table('codepay_order')->where('pay_id',$user['id'])->order('pay_time desc')->select();

        $this->assign('data',$data);
        return $this->fetch(':operations');
    }

    public function orders(){
        $user   = cmf_get_current_user();


        $OrdersModel = new OrdersModel();
        $order  = $OrdersModel->with('item')->order('id desc')->where('user_id',$user['id'])->select();

        //$album_order = Db::name('buy_log')->where('user_id',$user['id'])->select();
        $BuyLogModel = new BuyLogModel();
        $album_order = $BuyLogModel->with('album')->where('user_id',$user['id'])->order('id desc')->select();

        $OrdersSiModel = new OrdersSiModel();
        $order_si  = $OrdersSiModel->with('album')->order('id desc')->where('user_id',$user['id'])->select();
        

        $this->assign('order_si',$order_si);
        $this->assign('album_order',$album_order);
        $this->assign('order',$order);

        return $this->fetch(':orders');
    }

    public function password(){
        return $this->fetch(':password');
    }

    public function orderDetails(){
        $params = $this->request->param();

        $id = $params['id'];
        $user = cmf_get_current_user();
        $data = Db::name('orders')->where('id',$id)->where('user_id',$user['id'])->find();
        $item = Db::name('items')->where('id',$data['item_id'])->find();
        $this->assign('item',$item);
        $this->assign('data',$data);

        return $this->fetch(':order_details');
    }



}