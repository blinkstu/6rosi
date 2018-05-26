<?php	return array (
  'test/:id' => 
  array (
    0 => 'portal/Article/index?cid=1',
    1 => 
    array (
    ),
    2 => 
    array (
      'id' => '\d+',
      'cid' => '\d+',
    ),
  ),
  'test' => 
  array (
    0 => 'portal/List/index?id=1',
    1 => 
    array (
    ),
    2 => 
    array (
      'id' => '\d+',
    ),
  ),
  'album/:id' => 'main/albums/index',
  'cat/:id' => 'main/albums/cat',
  'store/list/:type' => 'main/index/store_list',
  'store/:id' => 'main/index/detials',
  'download/:id' => 'main/download/index',
  'orderform/:id' => 'main/order/orderform',
  'orderformonly/:id' => 'main/order/orderFormOnly',
);