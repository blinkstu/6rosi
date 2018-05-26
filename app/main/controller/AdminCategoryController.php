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

class AdminCategoryController extends AdminBaseController
{
    public function index(){
        $data = Db::name('AlbumsCat')->select();

        $this->assign('data',$data);

        return $this->fetch();
    }

}
