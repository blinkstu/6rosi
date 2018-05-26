<?php

namespace app\user\model;

use think\Model;

class OrdersSiModel extends Model {

    public function album()
    {
        return $this->hasOne('AlbumsSiModel','id' ,'item_id');
    }

}
?>
