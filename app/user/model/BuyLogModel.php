<?php

namespace app\user\model;

use think\Model;

class BuyLogModel extends Model {

    public function album()
    {
        return $this->hasOne('AlbumsModel','id' ,'albums_id');
    }

}
?>
