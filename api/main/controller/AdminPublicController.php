<?php
namespace api\main\controller;

use cmf\controller\AdminRestController;
use think\Db;
use think\Validate;

class AdminPublicController extends AdminRestController
{
    public function add_cat()
    {
        $params = $this->request->param();
        Db::name('albums_cat')->insert(['category_name' => $params['category_name'], 'grade' => 1]);
        return $this->success('添加成功');
    }
    public function remove_cat()
    {
        $params = $this->request->param();
        if ($params['id'] == 10) {
            $this->error('私属分类你是删不掉的！');
        }
        Db::name('albums_cat')->where('id', $params['id'])->delete();
        return $this->success('删除成功');
    }
    public function edit_cat()
    {
        $params = $this->request->param();
        Db::name('albums_cat')->where('id', $params['id'])->update(['category_name' => $params['category_name']]);
        return $this->success('修改成功');
    }
    public function remove_album()
    {
        $params = $this->request->param();
        Db::name('albums')->where('id', $params['id'])->delete();
        return $this->success('删除成功');
    }

    public function remove_items()
    {
        $params = $this->request->param();
        Db::name('items')->where('id', $params['id'])->delete();
        return $this->success('删除成功');
    }

    public function add_help()
    {
        $params = $this->request->param();
        $validate = new Validate([
            'nickname' => 'require',
            'url' => 'url',
            'wanted' => 'require',
            'big_pic' => 'require',
            'btn_text' => 'require',
        ]);

        $validate->message([
            'nickname.require' => '请填写昵称！',
            'url' => '请输入有效的跳转地址!',
            'wanted.require' => '请填写您想要什么推荐!',
            'big_pic.require' => '需要图片!',
            'btn_text.require' => '需要按钮文字!',
        ]);

        if (!$validate->check($params)) {
            $this->error($validate->getError());
        }

        $result = Db::name('helps')->insert([
            'nickname' => $params['nickname'],
            'url' => $params['url'],
            'wanted' => $params['wanted'],
            'big_pic' => $params['big_pic'],
            'btn_text' => $params['btn_text'],
        ]);

        if ($result != null) {
            return $this->success('添加成功');
        }

    }

    public function delete_help()
    {
        $params = $this->request->param();
        Db::name('helps')->where('id', $params['id'])->delete();

        return $this->success('删除成功');
    }

    public function change_coin()
    {
        $params = $this->request->param();
        Db::name('user')->where('id',$params['id'])->update(['coin'=>$params['coin']]);

        return $this->success('修改成功');
    }
}
