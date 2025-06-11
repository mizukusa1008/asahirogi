<?php
require_once(dirname(__FILE__).'/include/modelEllenaCatalogGift2507.php');
$model = new modelEllenaCatalogGift2507();
$model->execLogOutput();//ログ出力

if(isset($_GET['flg'])){
    //募集終了時はDBコメントを自動で上書き
    if($_GET['flg'] == 'closed'){
        $model->setClosedTableComment();
    }
}
