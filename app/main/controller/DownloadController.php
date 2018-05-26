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
namespace app\main\controller;

use cmf\controller\UserBaseController;
use think\Db;

class DownloadController extends UserBaseController
{

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 下载中心
     *
     * **/

    public function index()
    {
        $params = $this->request->param();

        if (empty($params['id'])) {
            return $this->error('缺少参数');
        }
        $id = $params['id'];
        $user = cmf_get_current_user();
        $user= Db::name('user')->where('id',$user['id'])->find();
        $album = Db::name('albums')->where('id', $id)->find();

        if ($album == null) {
            return $this->error('404');
        }

        $this->assign('album', $album);

        $check = Db::name('buy_log')->where('user_id', $user['id'])->where('albums_id', $id)->find();
        $check_coin = $user['coin'] >= $album['price'];
        $check_file = file_exists(ROOT_PATH . 'public/upload/' . $album['download_address']);

        if ($album['download_address'] == '' || !$check_file) {
            $data = [
                'state' => 4,
                'msg' => '资源不存在或出错，无法下载',
            ];
        }else if ($check != null) {
            $data = [
                'state' => 2,
                'msg' => '您已经购买过此图集，本次下载免费',
            ];
        } else if (!$check_coin) {
            $data = [
                'state' => 3,
                'msg' => '您的金币不足，无法下载',
            ];
        } else {
            $data = [
                'state' => 1,
                'msg' => '您现在准备下载图集 ' . $album['title'] . ' , 将扣除您 ' . $album['price'] . ' 个金币',
            ];
        }

        $only = false;
        if ($check != null && $only == true){
            
        }

        $this->assign('data', $data);
        return $this->fetch(':download');
    }

    public function getUrlPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();

            //检测条件
            if (empty($data['id'])) {
                return $this->error('缺少参数');
            }
            $id = $data['id'];
            $user = cmf_get_current_user();
            $user= Db::name('user')->where('id',$user['id'])->find();
            $album = Db::name('albums')->where('id', $id)->find();

            if ($album == null) {
                return $this->error('404');
            }

            $url = '/upload/' . $album['download_address'];

            //检测是否已经购买
            $check = Db::name('buy_log')->where('user_id', $user['id'])->where('albums_id', $id)->find();
            if ($check != null) {
                $result = [
                    'msg' => '您已经购买过，此次下载将不再扣取金币',
                    'url' => $url,
                ];
                return $this->success($result);
            }

            //检测金币是否足够
            if ($user['coin'] < $album['price']) {
                return $this->error('金币不足');
            }

            //记录此次购买
            $log = [
                'user_id' => $user['id'],
                'albums_id' => $id,
                'coins' => $album['price'],
                'date' => time(),
            ];
            Db::name('buy_log')->insert($log);

            //扣取金币
            Db::name('user')->where('id', $user['id'])->setDec('coin', $album['price']);

            //返回下载地址
            $result = [
                'msg' => '即将开始下载',
                'url' => $url,
            ];
            return $this->success($result);

        }
    }

}
