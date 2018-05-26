<?php

namespace app\main\controller;

use cmf\controller\UserBaseController;
use think\Db;

class OrderController extends UserBaseController
{


    public function orderForm()
    {
        $params = $this->request->param();
        if (empty($params['id'])) {
            return $this->error('缺少参数');
        }

        

        $id = $params['id'];
        $item = Db::name('items')->where('id', $id)->find();

        $user = cmf_get_current_user();
        $check = Db::name('orders')->where('user_id', $user['id'])->where('item_id', $id)->find();
        if ($check != null) {
            return $this->success('您已经买过此商品啦');
        }

        if ($item == null) {
            return $this->error('错误404');
        }

        $this->assign('item', $item);

        return $this->fetch(':orderform');
    }

    public function orderPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();

            //检测条件
            if (empty($data['id'])) {
                return $this->error('缺少参数');
            }
            $id = $data['id'];
            $user = cmf_get_current_user();
            $user = Db::name('user')->where('id', $user['id'])->find();
            $item = Db::name('items')->where('id', $id)->find();

            if ($item == null) {
                return $this->error('404');
            }

            //检测是否已经购买
            $check = Db::name('orders')->where('user_id', $user['id'])->where('item_id', $id)->find();
            if ($check != null) {
                return $this->success('您已经买过此商品啦');
            }

            //整理地址
            $province = $data['province'];
            $city = $data['city'];
            $block = $data['block'];
            $address = $data['address'];
            $address = $province . ' ' . $city . ' ' . $block . ' ' . $address ;

            //检测金币是否足够
            if ($user['coin'] < $item['price']) {
                return $this->error('金币不足');
            }
            //记录此次购买
            $log = [
                'type' => 1,
                'user_id' => $user['id'],
                'item_id' => $id,
                'price' => $item['price'],
                'address' => $address,
                'phone'=>$data['post']['phone_number'],
                'addressee'=>$data['post']['addressee'],
                'date' => time(),
            ];
            Db::name('orders')->insert($log);

            //扣取金币
            Db::name('user')->where('id', $user['id'])->setDec('coin', $item['price']);

            //去除库存
            Db::name('items')->where('id',$id)->setDec('stock',1);

            return $this->success('购买成功');
        }
    }

    public function orderFormOnly(){
        $params = $this->request->param();
        $id = $params['id'];
        $album = Db::name('albums_si')->where('id',$id)->find();
        if($album == null){
            return $this->error('404错误');
        }

        //检测是否已购买
        $user = cmf_get_current_user();
        $check = Db::name('orders_si')->where('user_id', $user['id'])->where('item_id', $id)->find();
        if ($check != null) {
            $check_file = file_exists(ROOT_PATH . 'public/upload/' . $album['download_address']);
            if($check_file){
                $download_url = $album['download_address'];
            }
            $this->assign('album',$album);
            $this->assign('order',$check);
            $this->assign('download_url',$download_url);
            return $this->fetch(':onlydownload');
        }

        $this->assign('album',$album);
        return $this->fetch(':orderformonly');
    }

    public function orderFromOnlyPost(){
        if ($this->request->isPost()) {
            $data = $this->request->param();

            //检测条件
            if (empty($data['id'])) {
                return $this->error('缺少参数');
            }
            $id = $data['id'];
            $user = cmf_get_current_user();
            $user = Db::name('user')->where('id', $user['id'])->find();
            $album = Db::name('albums_si')->where('id', $id)->find();

            if ($album == null) {
                return $this->error('404');
            }

            //检测是否已经购买
            $check = Db::name('orders')->where('user_id', $user['id'])->where('item_id', $id)->find();
            if ($check != null) {
                return $this->success('您已经买过此商品啦');
            }

            //整理地址
            $province = $data['province'];
            $city = $data['city'];
            $block = $data['block'];
            $address = $data['address'];
            $address = $province . ' ' . $city . ' ' . $block . ' ' . $address ;

            //检测金币是否足够
            if ($user['coin'] < $album['price']) {
                return $this->error('金币不足');
            }
            //记录此次购买
            $log = [
                'type' => 1,
                'user_id' => $user['id'],
                'item_id' => $id,
                'price' => $album['price'],
                'address' => $address,
                'phone'=>$data['post']['phone_number'],
                'addressee'=>$data['post']['addressee'],
                'date' => time(),
            ];
            Db::name('orders_si')->insert($log);

            //扣取金币
            Db::name('user')->where('id', $user['id'])->setDec('coin', $album['price']);

            //去除库存
            Db::name('albums_si')->where('id',$id)->setDec('stock',1);

            return $this->success('购买成功');
        }
    }
}
