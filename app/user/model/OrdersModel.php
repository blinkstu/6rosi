<?php

namespace app\user\model;

use think\Model;

class OrdersModel extends Model {

    public function item()
    {
        return $this->hasOne('ItemsModel','id' ,'item_id');
    }

}
?>
