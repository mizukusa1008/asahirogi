<?php
//初期処理
require_once('include/config.php');
require_once('include/config_log.php');
require_once(DIR_INCLUDE.'/model.php');
require_once(DIR_INCLUDE.'/templateContainer.php');

class pageAdmin {

    private $tmpl;
    private $model;

    const API_ADMIN_URL = API_DATA_BASE_URL . '/apiAdminData.php';

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

        // フィルター条件を取得
        $dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : '';
        $dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : '';
        $shopId = isset($_GET['shop_id']) ? $_GET['shop_id'] : '';

        // API経由で情報取得
        $apiParams = [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'shop_id' => $shopId
        ];
        $res = $this->model->asfileGetContents(self::API_ADMIN_URL, $apiParams);
        $adminData = json_decode($res, true);

        // 商品情報CSVを読み込む
        $catalogItems = $this->loadItemData();

        // テーブルHTML生成
        $tableHtml = $this->createTableHtml($adminData, $catalogItems);

        // 値挿入
        $this->tmpl->contents->addVars('_contents', array(
            'NOW_TXT' => $dateNow->format('Y年n月j日 H:i'),
            'PERIOD' => $dateBgn->format('Y年n月j日').'～'.$dateEnd->format('Y年n月j日'),
            'PERIOD_TXT' => $dateEnd->format('Y年n月j日 H:i:s').' まで',
            'DATE_FROM' => $dateFrom,
            'DATE_TO' => $dateTo,
            'SHOP_OPTIONS' => $shopOptions,
            'SELECTED_SHOP' => $shopId,
            'TABLE_HTML' => $tableHtml,
            'TOTAL_COUNT' => isset($adminData['total_count']) ? $adminData['total_count'] : 0
        ));
    }

    // 商品データをCSVから読み込む
    private function loadItemData() {
        $catalogItems = [];
        
        // カタログアイテムCSV読み込み
        $catalogCsv = $this->model->getCsv(DIR_INCLUDE.'/choiceList/ellena_catalog_items.csv');
        foreach ($catalogCsv as $item) {
            if (isset($item['c_item']) && isset($item['item_name'])) {
                $catalogItems[$item['c_item']] = $item['item_name'];
            }
        }
        
        // ひまわりアイテムCSV読み込み
        $himawariCsv = $this->model->getCsv(DIR_INCLUDE.'/choiceList/ellena_himawari_items.csv');
        foreach ($himawariCsv as $item) {
            if (isset($item['item_id']) && isset($item['item_name'])) {
                $catalogItems[$item['item_id']] = $item['item_name'];
            }
        }
        
        return $catalogItems;
    }

    // テーブルHTML生成
    private function createTableHtml($adminData, $catalogItems) {
        if (!isset($adminData['data']) || !is_array($adminData['data']) || empty($adminData['data'])) {
            return '<tr><td colspan="5" class="text-center">データがありません</td></tr>';
        }

        $shopList = [];
        $shopCsv = $this->model->getCsv(DIR_INCLUDE.'/choiceList/ellena_shops.csv', ['user_id', 'shop_name']);
        foreach ($shopCsv as $shop) {
            $shopList[$shop['user_id']] = $shop['shop_name'];
        }

        $html = '';
        foreach ($adminData['data'] as $index => $item) {
            $entryDate = isset($item['entry_ts']) ? date('Y-m-d H:i', strtotime($item['entry_ts'])) : '';
            $receiptNum = isset($item['receipt_num']) ? htmlspecialchars($item['receipt_num']) : '';
            
            $shopName = '不明';
            if (isset($item['user_id']) && isset($shopList[$item['user_id']])) {
                $shopName = htmlspecialchars($shopList[$item['user_id']]);
            }
            
            $itemName = '不明';
            if (isset($item['c_item']) && isset($catalogItems[$item['c_item']])) {
                $itemName = htmlspecialchars($catalogItems[$item['c_item']]);
            }
            
            $itemCode = isset($item['c_item']) ? htmlspecialchars($item['c_item']) : '';

            $html .= '<tr>';
            $html .= '<td>'.($index + 1).'</td>';
            $html .= '<td>'.$entryDate.'</td>';
            $html .= '<td>'.$receiptNum.'</td>';
            $html .= '<td>'.$shopName.'</td>';
            $html .= '<td>'.$itemCode.'</td>';
            $html .= '<td>'.$itemName.'</td>';
            $html .= '</tr>'.PHP_EOL;
        }

        return $html;
    }
}

new pageAdmin(); // 表示
?>