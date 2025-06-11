<?php
ini_set('display_errors', 0);
ini_set('memory_limit', '300M');
error_reporting(E_ALL ^ E_NOTICE); //エラーログにNOTICEは含めない

date_default_timezone_set('Asia/Tokyo'); //タイムゾーンの設定

define('DIR_INCLUDE', dirname(__FILE__) . '/');
define('DIR_INCLUDE_LIB', DIR_INCLUDE . 'lib/');

define('DIR_IMG_UPLOADTMP',  dirname(dirname(__FILE__)) . '/img/uploadtmp/');

//環境
define('SITE_STAGE', 'dev'); //prd:本番環境, dev:開発環境
//サブアプリ名
define('APP_NAME', 'ellena_cataloggift2507');

define('SITE_HOME_URL', 'https://www.ellena.co.jp/'); //フォーム募集元サイト



//エラーコード定数
define('ERR_CD_MALE_FAILED_USER', '101'); //メール送信失敗（ユーザ）
define('ERR_CD_MALE_FAILED_OWNER', '102'); //メール送信失敗（担当者）
define('ERR_CD_CSV_FAILED', '201'); //CSV作成失敗
define('ERR_CD_DB_FAILED', '202'); //DB登録失敗
define('ERR_CD_FILE_FAILED', '203'); //ファイル登録失敗
define('ERR_ITEM_END', '204'); //登録商品期限切れ

//各種ページ
define('PAGE_INDEX', 'index.php'); //index(コントローラ)
define('PAGE_AGREEMENT', 'agreement.php'); //参加ページ
define('PAGE_ENTRY', 'entry.php'); //参加ページ
define('PAGE_CONFIRMATION', 'confirmation.php'); //確認ページ
define('PAGE_MAIL_SEND', 'mail_send.php'); //メール送信完了ページ
define('PAGE_COMING_SOON', 'before.php'); //応募開始前ページ（）
define('PAGE_CLOSED', 'closed.php'); //応募終了後ページ
define('PAGE_ORDER_LIST', 'order_admin.php'); //受注状況確認ページ
define('PAGE_ADMIN', 'admin.php');

define('PAGE_LOGVIEWER', 'logviewer.php'); //ログ確認ページ

//APIベースURL
$folderName = 'ellena';
if (SITE_STAGE == 'prd') {
	define('API_ROOT_URL', 'http://192.168.21.70/');
	define('MAIL_API_ROOT_URL', 'http://192.168.21.71/mailAPI/');
	define('SITE_HOME_URL_2', 'https://sform.ascon.co.jp/ellena/ellena_qr/'); //QR読み取りページ
} else {
	define('API_ROOT_URL', 'http://localhost:88/form/');
	define('MAIL_API_ROOT_URL', 'http://localhost:88/form/_MAILAPI/');
	define('SITE_HOME_URL_2', 'http://localhost:88/form/ellena/ellena_qr/'); //開発QR読み取りページ
}
define('API_DATA_BASE_URL', API_ROOT_URL . $folderName . '/api_cataloggift2507');

define('API_DATA_STORAGE_URL', API_DATA_BASE_URL . '/registerEllenaCatalogGift2507.php'); //情報保存用
define('API_DATA_LOGOUTPUT_URL', API_DATA_BASE_URL . '/logoutputEllenaCatalogGift2507.php'); //応募状況取得
define('API_DATA_ORDER_ADMIN_URL', API_DATA_BASE_URL . '/apiOrderAdmin.php');
define('API_ORDER_ADMIN_COMMENT_URL', API_DATA_BASE_URL . '/apiOrderAdminComment.php');

define('API_ID_CHECK_URL', API_DATA_BASE_URL . '/apiIdEllenaCatalogGift2507.php'); //ID正誤チェック
// define('API_ID2_CHECK_URL', API_DATA_BASE_URL.'/apiId2EllenaCatalogGift2507.php');//カタログID正誤チェック
define('API_USED_ID_CHECK_URL', API_DATA_BASE_URL . '/apiUsedIdEllenaCatalogGift2507.php'); //使用済IDチェック
// define('API_ITEM_CHECK_URL', API_DATA_BASE_URL.'/apiItemEllenaCatalogGift2507.php');//商品番号チェック

//shopCsv
define('CSV_S', DIR_INCLUDE . '/choiceList/shop_list.csv');

//メール機能使用、不使用
define('SENDMAIL_FLG', true);	//false:sendMail use , true:sendMail use
define('OWNERMAIL_FLG', false);	//false:ownerMail use , true:ownerMail use

//メール共通設定
define('SENDMAIL_API_URL', MAIL_API_ROOT_URL . '/send_a_mail.php');
define('SENDMAIL_MAIL_TMPL', 'mail.user.tmpl.html'); //メールテンプレート
define('OWNER_MAIL_TMPL', 'mail.owner.tmpl.html'); //メールテンプレート
define('MAIL_FROM_EMAIL', 'no-reply@ascon.co.jp'); //差出人メルアド

//パラメータによる処理分け
define('PRM_FLG', true);	//false:no use , true:use
define('GET_KEY', 'c');
define('GET_ID', 'id');
define('GET_ITEM', 'item');
define('GET_USER', 'user');
define('GET_QR', 'q');
$catalogType = ['himawari', 'catalog'];
if (PRM_FLG) {
	if (isset($_GET[GET_KEY])) {
		if (trim($_GET[GET_KEY]) && in_array($_GET[GET_KEY], $catalogType)) {
			define('CP_PRM', $_GET[GET_KEY]);
		}
	}
	if (defined('CP_PRM')) {
		if (isset($_GET[GET_KEY])) {
			if (trim($_GET[GET_KEY]) == 'catalog') { //カタログギフト選択時の定数設定
				define('HEADER_TITLE', 'エレナのカタログギフト');
				define('CUSTOM_ENTRY_PERIOD_END', '2025/08/19/23:59:59'); //応募終了日
				define('IGNORE_DELIV_DAY', '2025/08/10/23:59:59'); //配達希望日入力終了日
				define('DELIV_PERIOD_END', '2025/09/10/23:59:59'); //配達希望日終了
				define('AGREE_DELIV_SELECT_END_DAY', '※お届け日のご指定は12月10日(火)23:59までのご注文分とさせていただきます。<br/>
それ以降のご注文につきましては、お届け日をご指定いただけませんので、あらかじめご了承ください。');
			} else {
				define('HEADER_TITLE', 'エレナの選べるギフト'); //カタログギフト選択時の定数設定
				define('CUSTOM_ENTRY_PERIOD_END', '2025/08/31/23:59:59'); //応募終了日
				define('IGNORE_DELIV_DAY', '2025/08/31/23:59:59'); //配達希望日入力終了日
				define('DELIV_PERIOD_END', '2025/08/15/23:59:59'); //配達希望日終了
				define('AGREE_DELIV_SELECT_END_DAY', '※年内お届け分の最終受付は12月23日(月)23:59までとさせていただきます。<br/>
　それ以降のご注文は年明けのご到着となります。<br/>
※12月25日(水)～1月10日(金)のお届け日指定はできません。');
			}
		}
		$file = dirname(__FILE__) . '/config_' . CP_PRM . '.php';
		if (file_exists($file)) {
			include($file);
		}
	} else {
		if ($_GET[GET_KEY] === 'log' || $_GET[GET_KEY] === 'order_admin') {
		} else {
			header("Location: " . SITE_HOME_URL);
			exit();
		}
		// var_dump($_GET[GET_KEY]);
		// var_dump($catalogType);

	}
	// }else{




	/****************************************************************************
	 メール設定　user
	 ***************************************************************************/
	//タイトル
	define('SENDMAIL_FORM_SUBJECT', HEADER_TITLE . ' 受付完了メール');
	//差出人名
	define('SENDMAIL_FROM_NAME', 'エレナギフト事務局');
	define('JIMU_NAME', SENDMAIL_FROM_NAME);
	/****************************************************************************
	 メール設定　owner
	 ***************************************************************************/
	//タイトル
	define('OWNER_FORM_SUBJECT', '入力フォームからの応募がありました。');
	//差出人名
	define('OWNER_FROM_NAME', SENDMAIL_FROM_NAME);
	//送り先メルアド
	$ownerArr = array('mizukusa@ascon.co.jp');
	$ownerStr = implode(',', $ownerArr);
	define('OWNER_TO_MAIL', $ownerStr);
}

// //ファイル使用有無
// define('FILE_FLG',false);	//false:no use , true:use
// //ファイル複数
// define('FILE_MULTIPLE_FLG',false);	//false:no use , true:use
// if(FILE_FLG && FILE_MULTIPLE_FLG){
// 	define('FILE_MULTIPLE_STR','multiple');
// }else{
// 	define('FILE_MULTIPLE_STR','');
// }

//フォーム表示タイマー
define('ENTRY_PERIOD_BGN', '2025/07/01/00:00:00'); //応募開始日時
define('ENTRY_PERIOD_END', CUSTOM_ENTRY_PERIOD_END); //応募終了日時
// define('ENTRY_PERIOD_BGN', '2024/08/17/00:00:00');//応募開始日時
// define('ENTRY_PERIOD_END', '2024/08/31/23:59:59');//応募終了日時

define('DELIV_BEFORE_START', '2025/08/15/23:59:59'); //配達開始前日
define('DELIV_HOLIDAY_STRAT', '2025/08/09/00:00:00'); //配達休止開始日
define('DELIV_HOLIDAY_END', '2025/08/17/23:59:59'); //配達休止終了日

//レシート有効期間
define('IMG_PERIOD_BGN', ENTRY_PERIOD_BGN); //応募開始日時
define('IMG_PERIOD_END', ENTRY_PERIOD_END); //応募終了日時

//事務局 開設期間
define('JIMU_PERIOD_BGN', ENTRY_PERIOD_BGN); //応募開始日時
define('JIMU_PERIOD_END', ENTRY_PERIOD_END); //応募終了日時

//事務局 休暇期間
define('JIMU_HOLIDAY_FLG', true); //休暇期間が存在するか
define('JIMU_HOLIDAY_PERIOD_BGN', '2025/08/09/00:00:00'); //応募開始日時
define('JIMU_HOLIDAY_PERIOD_END', '2025/08/17/23:59:59'); //応募終了日時

//ログ確認ページのパスワード
define('LOGVIEWER_USER', 'ellena');
define('LOGVIEWER_PW', 'CatalogGift2507');

//配達希望日 開始日設定
define('DELIV_ADD', 14);

//年齢　今年からAGE_MAX年前までプルダウン生成
define('AGE_MAX', 120);

define('SAME_STR', '注文者様の住所に送る');
define('ANOTHER_STR', '別の住所に送る');

//配達希望
$subArr = [
	'sub_name_sei' => 'お届け先 名前',
	'sub_name_mei' => 'お届け先 名前',
	'sub_kana_sei' => 'お届け先 フリガナ',
	'sub_kana_mei' => 'お届け先 フリガナ',
	'sub_zipcode' => 'お届け先 郵便番号',
	'sub_pref' => 'お届け先 都道府県',
	'sub_pref_name' => 'お届け先 都道府県名',
	'sub_address1' => 'お届け先 住所1',
	'sub_address2' => 'お届け先 住所2',
	'sub_telphone' => 'お届け先 電話番号'
];
define('SUB_ARR', serialize($subArr));
//カタログ種類
define('CATALOG_TYPE', serialize($catalogType));
//配達希望
$delivArr = ['指定しない', '指定する'];
define('DELIV_ARR', serialize($delivArr));
//配達指定不可商品
$delivNotPussible = ['1001', '1002'];
define('DELIV_NOT_POSSIBLE', json_encode($delivNotPussible));

//有無
$existArr = ['なし', 'あり'];
define('EXIST_ARR', serialize($existArr));

//のし
$noshiTypeArr = ['お中元', '御礼', '無地(祝のし)','お供え','無地(仏のし)'];
define('NOSHI_TYPE_ARR', serialize($noshiTypeArr));
//のし不可商品番号
$noshiIgnoreItem = ['T-01', 'T-02', 'Y-14', '2001', '2002', '2003', '2004', '6501', '6503', '6502'];
define('NOSHI_IGNORE_ITEM', json_encode($noshiIgnoreItem));


//店舗アドレスドメイン名 DB初期化の際に一緒に切り替え！！！！！！！！！！
//define('MAIL_DOMAIN', '@ellena.co.jp');
define('MAIL_DOMAIN','@ascon.co.jp');

//質問
$questionArr = array("テレビ・ラジオ", "雑誌・新聞・チラシ", "インターネット・SNS", "口コミ・その他");
define('QUESTION_ARR', serialize($questionArr));

// ヘッダーは画像であるか
define('HEAD_IMG_FLG', true);

// googleTagManager false:OFF true:ON
define('GTM_FLG', false);

//false：受付期間前ページ無効、treu：受付期間前ページ有効
$timeBeforeFlg = false;

switch (basename($_SERVER['SCRIPT_FILENAME'])) {
	//case PAGE_AGREEMENT:
	case PAGE_COMING_SOON:
	case PAGE_CLOSED:
	case PAGE_LOGVIEWER:
	case PAGE_ORDER_LIST:
	case PAGE_ADMIN:
		break;
	default:
		//その他
		require_once(DIR_INCLUDE . '/pageTimeKeeper.php');
		$addPrm = PRM_FLG ? '?' . GET_KEY . '=' . $_GET[GET_KEY] : '';
		if (!$timeBeforeFlg) {
			$timeKeeper = new pageTimeKeeper('', PAGE_CLOSED . $addPrm);
		} else {
			$timeKeeper = new pageTimeKeeper(PAGE_COMING_SOON . $addPrm, PAGE_CLOSED . $addPrm);
		}
		$timeKeeper->execRedirectTimer(ENTRY_PERIOD_BGN, ENTRY_PERIOD_END);
}
define('YAKKAN_TITLE', 'エレナギフト'); //カタログギフト選択時の定数設定
//セッション
define('APP_SESSION_NAME', strtoupper('phpsessid_' . APP_NAME));
session_name(APP_SESSION_NAME);
session_start();//セッション開始
