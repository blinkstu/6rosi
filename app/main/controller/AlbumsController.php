<?php

namespace app\main\controller;

use cmf\controller\HomeBaseController;
use think\Db;

class AlbumsController extends HomeBaseController
{
    public function index()
    {
        $params = $this->request->param();

        $si = false;
        if(isset($params['si'])){
            $si = true;
        }

        if (empty($params['id'])) {
            $this->error('没有ID');
        }

        $data = Db::name('albums')->where('id', $params['id'])->find();
        Db::name('albums')->where('id', $params['id'])->where('category_id', '<>', 10)->setInc('views');
        if ($si) {
            $data = Db::name('albums_si')->where('id', $params['id'])->find();
            Db::name('albums_si')->where('id', $params['id'])->setInc('views');
            $this->assign('si',true);
        }
        if ($data == null) {
            abort(404,'页面不存在');
        }
        $pics = json_decode($data['small_pics'], true);
        $data['photos'] = $pics;

        $rand = Db::query("select * from cmf_albums ORDER BY rand() LIMIT 8");


        $this->assign('rand', $rand);
        $this->assign('data', $data);
        return $this->fetch(':albums');
    }

    public function cat()
    {
        $params = $this->request->param();

        if (empty($params['id'])) {
            $this->error('分类ID');
        }

        $data = Db::name('albums')->where('category_id', $params['id'])->order('id desc')->paginate(12);
        $list = $data->render();

        if ($params['id'] == 10) {
            $data = Db::name('albums_si')->where('category_id', $params['id'])->order('id desc')->paginate(12);
            $list = $data->render();
        }

        if (count($data) == 0) {
            $this->error('404未找到');
        }

        $this->assign('cat_id',$params['id']);

        $this->assign('data', $data);
        $this->assign('list', $list);
        return $this->fetch(':cat');
    }
}
