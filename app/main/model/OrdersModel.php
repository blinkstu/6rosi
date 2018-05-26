<?php

namespace app\main\model;

use think\Model;

class OrdersModel extends Model {

    public function item()
    {
        return $this->hasOne('ItemsModel','id' ,'item_id');
    }

    public function user()
    {
        return $this->hasOne('UserModel','id','user_id');
    }


}
?>
