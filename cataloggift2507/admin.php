<?php
//初期処理
require_once('include/config.php');
require_once('include/config_log.php');
require_once(DIR_INCLUDE.'/model.php');
require_once(DIR_INCLUDE.'/templateContainer.php');

class pageAdmin {

    private $tmpl;
    private $model;

    //コンストラクタ
    function __construct() {
        // class読み込み
        $this->model = new formModel();
        //ダイジェスト認証
        $this->model->digestLogin();

        $this->tmpl = new templateContainer('admin');
        $this->_assign();
        $this->tmpl->displayContent();
    }

    function _assign() {
        $dateNow = new DateTime();
        $dateBgn = new DateTime(ENTRY_PERIOD_BGN);
        $dateEnd = new DateTime(ENTRY_PERIOD_END);

        // 店舗リストを取得
        $shopList = $this->model->getCsv(DIR_INCLUDE.'/choiceList/ellena_shops.csv', ['user_id', 'shop_name']);
        $shopOptions = '';
        foreach ($shopList as $shop) {
            $shopOptions .= '<option value="'.$shop['user_id'].'">'.$shop['shop_name'].'</option>'.PHP_EOL;
        }

        // APIエンドポイントの設定 - 相対パスのみを使用し、サーバー情報を公開しない
        $apiEndpoint = 'api_endpoint.php';
        
        // 値挿入
        $this->tmpl->contents->addVars('_contents', array(
            'NOW_TXT' => $dateNow->format('Y年n月j日 H:i'),
            'PERIOD' => $dateBgn->format('Y年n月j日').'～'.$dateEnd->format('Y年n月j日'),
            'PERIOD_TXT' => $dateEnd->format('Y年n月j日 H:i:s').' まで',
            'SHOP_OPTIONS' => $shopOptions,
            'API_ENDPOINT' => $apiEndpoint
        ));
    }
}

new pageAdmin(); // 表示
?>