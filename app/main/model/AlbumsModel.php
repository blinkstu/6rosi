<?php

namespace app\main\model;

use think\Model;

class AlbumsModel extends Model {

    public function category()
    {
        return $this->hasOne('AlbumsCatModel','id' ,'category_id');
    }

}
?>
