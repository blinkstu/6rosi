<?php

namespace app\main\controller;

use cmf\controller\HomeBaseController;
use think\Db;

class CodepayController extends HomeBaseController
{
    public function index()
    {
        $params = $this->request->param();
        $sign = '';
        foreach ($_POST as $key => $val) {
            if ($val == '') {
                continue;
            }
            //跳过空值
            if ($key != 'sign') { //跳过sign
                $sign .= "$key=$val&"; //拼接为url参数形式
            }
        }
        if (!$params['pay_no'] || md5(substr($sign, 0, -1) . "KocKTUbHHxka9rx1YBPpNdJFmdt6ri98") != $params['sign']) { //KEY密钥为你的密钥
            
            exit('fail');
        } else { //合法的数据
            //业务处理
            // $_POST['pay_id'] 这是付款人的唯一身份标识或订单ID
            // $_POST['pay_no'] 这是流水号 没有则表示没有付款成功 流水号不同则为不同订单
            // $_POST['money'] 这是付款金额
            //  $_POST['param'] 这是自定义的参数
            $coin  = round($params['money']);
            $user_id = $params['pay_id'];

            if(!empty($params['pay_no'])){
                Db::name('user')->where('id',$user_id)->setInc('coin',$coin);
                $this->success('支付成功');
            }else{
                return $this->error('支付出现错误');
            }
        }
    }

}
