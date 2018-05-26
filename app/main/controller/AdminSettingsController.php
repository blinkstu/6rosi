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

class AdminSettingsController extends AdminBaseController
{
    public function ads(){
        $data = Db::name('settings_ad')->select();



        $this->assign('data',$data);
        return $this->fetch();
    }

    public function savePost(){
        if ($this->request->isPost()) {
            $data   = $this->request->param();

            if(empty($data['pic'])){
                $this->error('请添加大图');
            }else{
                foreach ($data['ids'] as $key => $id) {
                    Db::name('settings_ad')->where('id',$id)->update([
                        'pic' => $data['pic'][$id]
                    ]);
                }
            }
            $this->success('保存成功');
        }
    }

    public function other(){
        return redirect('/admin/theme/files/theme/6rosi');
    }

}
