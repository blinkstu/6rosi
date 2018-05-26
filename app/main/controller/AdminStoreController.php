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

use cmf\controller\AdminBaseController;
use think\Db;

class AdminStoreController extends AdminBaseController
{
    public function index()
    {

        $data = Db::name('items')->order('id desc')->select();

        $this->assign('data', $data);

        return $this->fetch();
    }

    public function add()
    {
        return $this->fetch();
    }

    public function addPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $pics_data = [];

            if (empty($data['post']['type'])) {
                $this->error('请选择分类');
            }
            if (empty($data['post']['big_pic'])) {
                $this->error('请添加大图');
            }
            if (empty($data['post']['title'])) {
                $this->error('请输入清单');
            }
            if (empty($data['post']['price'])) {
                $this->error('请输入价格');
            }

            $data['post']['create_date'] = time();
            if (file_exists(ROOT_PATH . 'public/upload/' . $data['post']['big_pic']) && strrpos($data['post']['big_pic'], ".thumb1.") == false) {
                $image = \think\Image::open(ROOT_PATH . 'public/upload/' . $data['post']['big_pic']);
                $image->thumb(200, 200, \think\Image::THUMB_CENTER)->save(ROOT_PATH . 'public/upload/' . str_replace(".", ".thumb1.", $data['post']['big_pic']));
            }

            if (file_exists(ROOT_PATH . 'public/upload/' . $data['post']['big_pic']) && strrpos($data['post']['big_pic'], ".big.") == false) {
                rename(ROOT_PATH . 'public/upload/' . $data['post']['big_pic'], ROOT_PATH . 'public/upload/' . str_replace(".", ".big.", $data['post']['big_pic']));
                $data['post']['big_pic'] = str_replace(".", ".big.", $data['post']['big_pic']);
            }

            $id = Db::name('items')->insertGetId($data['post']);

            $this->success('添加成功!', url('AdminStore/edit', ['id' => $id]));
        }
    }

    public function edit()
    {
        $params = $this->request->param();
        $id = $params['id'];
        $data = Db::name('items')->where('id', $id)->find();
        $this->assign('data', $data);

        return $this->fetch();

    }

    public function editPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $pics_data = [];
            $id = $data['id'];

            if (empty($data['post']['type'])) {
                $this->error('请选择分类');
            }
            if (empty($data['post']['big_pic'])) {
                $this->error('请添加大图');
            }
            if (empty($data['post']['title'])) {
                $this->error('请输入清单');
            }
            if (empty($data['post']['price'])) {
                $this->error('请输入价格');
            }

            $data['post']['create_date'] = time();
            if (file_exists(ROOT_PATH . 'public/upload/' . $data['post']['big_pic']) && strrpos($data['post']['big_pic'], ".thumb1.") == false) {
                $image = \think\Image::open(ROOT_PATH . 'public/upload/' . $data['post']['big_pic']);
                $image->thumb(200, 200, \think\Image::THUMB_CENTER)->save(ROOT_PATH . 'public/upload/' . str_replace(".", ".thumb1.", $data['post']['big_pic']));
            }

            if (file_exists(ROOT_PATH . 'public/upload/' . $data['post']['big_pic']) && strrpos($data['post']['big_pic'], ".big.") == false) {
                rename(ROOT_PATH . 'public/upload/' . $data['post']['big_pic'], ROOT_PATH . 'public/upload/' . str_replace(".", ".big.", $data['post']['big_pic']));
                $data['post']['big_pic'] = str_replace(".", ".big.", $data['post']['big_pic']);
            }

            $id = Db::name('items')->where('id', $id)->update($data['post']);

            $this->success('修改成功!');
        }
    }

    public function orders(){
        
    }

}
