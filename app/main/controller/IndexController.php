<?php

namespace app\main\controller;

use cmf\controller\HomeBaseController;
use think\Db;

class IndexController extends HomeBaseController
{
    public function index()
    {
        $ad = Db::name('settings_ad')->where('id', 1)->find();

        $rand = Db::query("select * from cmf_albums ORDER BY rand() LIMIT 8");
        $rand2 = Db::query("select * from cmf_albums ORDER BY rand() LIMIT 8");
        $new = Db::field('title,id,big_pic,create_date,views,category_id')
            ->table('cmf_albums')
            ->union('SELECT title,id,big_pic,create_date,views,category_id FROM cmf_albums_si')
            ->order('create_date desc')
            ->limit(8)
            ->select();
        $hot = Db::name('albums')->order('views desc')->limit(8)->select();
        $recommend = Db::name('albums')->order('recommend desc')->limit(8)->select();

        $data['rand'] = $rand;
        $data['rand2'] = $rand;
        $data['new'] = $new;
        $data['hot'] = $hot;
        $data['recommend'] = $recommend;

        $this->assign('data', $data);
        $this->assign('ad', $ad);
        return $this->fetch(':index');
    }

    public function about()
    {
        return $this->fetch(':about');
    }

    public function models()
    {
        return $this->fetch(':models');
    }

    public function example()
    {
        return $this->fetch(':example');
    }

    public function store()
    {
        $red = Db::name('items')->where('type', 1)->order('id desc')->limit(20)->select();
        $blue = Db::name('items')->where('type', 2)->order('id desc')->limit(20)->select();

        $this->assign('red', $red);
        $this->assign('blue', $blue);
        return $this->fetch(':store');
    }

    public function store_list()
    {
        $params = $this->request->param();
        $type = $params['type'];

        $data = Db::name('items')->where('type', $type)->order('id desc')->limit(20)->paginate(43);

        if ($type == 1) {
            $pic_name = 'shopr';
        } else {
            $pic_name = 'shopd';
        }

        $this->assign('pic_name', $pic_name);

        $this->assign('data', $data);
        return $this->fetch(':lists');
    }

    public function detials()
    {
        $params = $this->request->param();
        $id = $params['id'];

        $data = Db::name('items')->where('id', $id)->find();
        $rand = Db::query("select * from cmf_albums ORDER BY rand() LIMIT 8");
        $this->assign('data', $data);
        $this->assign('rand', $rand);
        return $this->fetch(':detials');
    }

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

            //检测金币是否足够
            if ($user['coin'] < $item['price']) {
                return $this->error('金币不足');
            }
            $address = '';
            //记录此次购买
            $log = [
                'type' => 1,
                'user_id' => $user['id'],
                'item_id' => $id,
                'price' => $item['price'],
                'address' => $address,
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
}
