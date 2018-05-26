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

use app\main\model\AlbumsModel;
use cmf\controller\AdminBaseController;
use think\Db;

class AdminAlbumController extends AdminBaseController
{
    public function index()
    {

        //$data = Db::name('albums')->order('id desc')->select();
        $AlbumsModel = new AlbumsModel();
        $data = $AlbumsModel->with('category')->order('id desc')->select();
        //echo '<pre>';var_dump($data);
        $this->assign('data', $data);

        return $this->fetch();
    }

    public function si()
    {

        //$data = Db::name('albums')->order('id desc')->select();
        $data = Db::name('albums_si')->order('id desc')->select();
        //echo '<pre>';var_dump($data);
        $this->assign('data', $data);

        return $this->fetch();
    }

    public function add()
    {
        $cate_data = Db::name('albums_cat')->select();

        $this->assign('cate_data', $cate_data);
        return $this->fetch();
    }

    public function edit()
    {
        $params = $this->request->param();
        $id = $params['id'];

        $data = Db::name('albums')->where('id', $id)->find();
        $cate_data = Db::name('albums_cat')->select();

        if (!empty($params['si'])) {
            $data = Db::name('albums_si')->where('id', $id)->find();
            $cate_data = Db::name('albums_cat')->select();
            $this->assign('si', 'si');
        }

        $this->assign('cate_data', $cate_data);
        $this->assign('data', $data);

        $photos = json_decode($data['small_pics'], true);
        $this->assign('photos', $photos);

        $filepath = ROOT_PATH . 'public/upload/' . $data['download_address'];
        $path = pathinfo($filepath);

        $name = '';
        if (!empty($path['extension'])) {
            $name = $path['filename'] . '.' . $path['extension'];
        }

        $this->assign('name', $name);

        //echo '<pre>';var_dump($data);

        return $this->fetch();
    }

    public function addPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $pics_data = [];

            if (empty($data['post']['category_id'])) {
                $this->error('请选择分类');
            }
            if (empty($data['post']['big_pic'])) {
                $this->error('请添加大图');
            }

            if (!empty($data['photo_names']) && !empty($data['photo_urls'])) {

                foreach ($data['photo_urls'] as $key => $url) {
                    $image = \think\Image::open(ROOT_PATH . 'public/upload/' . $url);
                    $image->thumb(300, 200, \think\Image::THUMB_CENTER)->save(ROOT_PATH . 'public/upload/' . str_replace(".", ".thumb.", $url));
                    $photoUrl = cmf_asset_relative_url(str_replace(".", ".thumb.", $url));
                    array_push($pics_data, ["url" => $photoUrl, "name" => $data['photo_names'][$key]]);
                }
                
                $this->deletePhotos($data);

                $pics_json = json_encode($pics_data);
                $data['post']['small_pics'] = $pics_json;
            } else {
                $this->error('请添加图片');
            }
            if (file_exists(ROOT_PATH . 'public/upload/' . $data['post']['big_pic'])) {
                rename(ROOT_PATH . 'public/upload/' . $data['post']['big_pic'], ROOT_PATH . 'public/upload/' . str_replace(".", ".big.", $data['post']['big_pic']));
                $data['post']['big_pic'] = str_replace(".", ".big.", $data['post']['big_pic']);
            }

            if (file_exists(ROOT_PATH . 'public/upload/' . $data['post']['big_pic']) && strrpos($data['post']['big_pic'], ".big.")) {
                $image = \think\Image::open(ROOT_PATH . 'public/upload/' . $data['post']['big_pic']);
                $image->thumb(350, 233, \think\Image::THUMB_CENTER)->save(ROOT_PATH . 'public/upload/' . str_replace(".big.", ".small.", $data['post']['big_pic']));
            }

            if (!empty($data['post']['download_address'])) {
                $data['post']['download_address'] = $this->save_file($data['file_name'], $data['post']['download_address']);
            }

            $data['post']['views'] = 0;
            $data['post']['files'] = '';
            $data['post']['create_date'] = time();

            //var_dump($data['post']);
            $table = 'albums';
            if ($data['post']['category_id'] == 10) {
                $table = 'albums_si';
            }

            $id = Db::name($table)->insertGetId($data['post']);

            //判断是否是私属
            if ($data['post']['category_id'] == 10) {
                $this->success('添加成功!', url('AdminAlbum/edit', ['id' => $id, 'si' => 'si']));
            } else {
                $this->success('添加成功!', url('AdminAlbum/edit', ['id' => $id]));
            }

        }
    }
    public function editPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $pics_data = [];
            $id = $data['id'];

            if (empty($data['post']['category_id'])) {
                $this->error('请选择分类');
            }
            if (empty($data['post']['big_pic'])) {
                $this->error('请添加大图');
            }
            if (!empty($data['photo_names']) && !empty($data['photo_urls'])) {

                foreach ($data['photo_urls'] as $key => $url) {
                    if (strrpos($url, ".thumb.") == false) {
                        $image = \think\Image::open(ROOT_PATH . 'public/upload/' . $url);
                        $image->thumb(300, 200, \think\Image::THUMB_CENTER)->save(ROOT_PATH . 'public/upload/' . str_replace(".", ".thumb.", $url));
                        $photoUrl = cmf_asset_relative_url(str_replace(".", ".thumb.", $url));
                    } else {
                        $photoUrl = cmf_asset_relative_url($url);
                    }
                    array_push($pics_data, ["url" => $photoUrl, "name" => $data['photo_names'][$key]]);
                }
                //var_dump($data['photo_urls']);
                $this->deletePhotos($data);

                $pics_json = json_encode($pics_data);
                $data['post']['small_pics'] = $pics_json;
            }

            if (file_exists(ROOT_PATH . 'public/upload/' . $data['post']['big_pic']) && strrpos($data['post']['big_pic'], ".big.") == false) {
                rename(ROOT_PATH . 'public/upload/' . $data['post']['big_pic'], ROOT_PATH . 'public/upload/' . str_replace(".", ".big.", $data['post']['big_pic']));
                $data['post']['big_pic'] = str_replace(".", ".big.", $data['post']['big_pic']);
            }

            if (file_exists(ROOT_PATH . 'public/upload/' . $data['post']['big_pic']) && strrpos($data['post']['big_pic'], ".big.")) {
                $image = \think\Image::open(ROOT_PATH . 'public/upload/' . $data['post']['big_pic']);
                $image->thumb(350, 233, \think\Image::THUMB_CENTER)->save(ROOT_PATH . 'public/upload/' . str_replace(".big.", ".small.", $data['post']['big_pic']));
            }

            if (!empty($data['post']['download_address'])) {
                $data['post']['download_address'] = $this->save_file($data['file_name'], $data['post']['download_address']);
            }

            $data['post']['files'] = '';
            //$data['post']['create_date'] = time();

            //var_dump($data['post']);
            $table = 'albums';
            if ($data['post']['category_id'] == 10) {
                $table = 'albums_si';
            }

            Db::name($table)->where('id', $id)->update($data['post']);
            $this->success('保存成功!');
        }
    }

    private function deletePhotos($data)
    {
        foreach ($data['photo_urls'] as $key => $url) {
            if (strrpos($url, ".thumb.") == false) {
                if ($url != $data['post']['big_pic']) {
                    if (file_exists(ROOT_PATH . 'public/upload/' . $url)) {
                        unlink(ROOT_PATH . 'public/upload/' . $url);
                    }
                }
            }
        }
    }

    private function save_file($filename, $filedir)
    {
        $filepath = ROOT_PATH . 'public/upload/' . $filedir;
        $path = pathinfo($filepath);

        $filename = str_replace('.' . $path['extension'], '', $filename);

        if (strrpos($filedir, $filename) == true) {
            return $filedir;
        }

        $newpath = str_replace($path['filename'], $filename . '_' . $path['filename'], $filepath);
        rename($filepath, $newpath);
        $newsave = str_replace($path['filename'], $filename . '_' . $path['filename'], $filedir);

        return $newsave;
    }

}
