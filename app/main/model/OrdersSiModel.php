<?php

namespace app\main\model;

use think\Model;

class OrdersSiModel extends Model {

    public function album()
    {
        return $this->hasOne('AlbumsSiModel','id' ,'item_id');
    }

    public function user()
    {
        return $this->hasOne('UserModel','id','user_id');
    }

}
?>
