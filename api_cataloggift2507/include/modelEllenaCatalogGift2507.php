<?php
require_once(dirname(__FILE__) . '/config.php');
require_once(dirname(__FILE__) . '/classDB.php');
/**
 * DB操作クラス
 */
class modelEllenaCatalogGift2507
{

	private $dbOpeName;

	private $entrySid;
	//private $entryDisplayId;
	private $entryTs = NULL;
	private $db;

	public $fileRegistPath = NULL;

	public function __construct()
	{
		//DBオブジェクト作成
		$this->db = new classDB();
		$this->dbOpeName = 'api_' . APP_NAME;
	}

	/**
	 * データ追加処理
	 */
	public function execPostDataRegist()
	{
		$ret = false;
		$fileRet = false;
		//WEB応募フォーム側の登録日時取得
		$entryTimeStamp = isset($_POST['entry_ts']) ? $_POST['entry_ts'] : '';

		$this->entryTs = new DateTime($entryTimeStamp);

		if ($this->entryTs) {
			$this->entrySid = $this->getNextVal();
			$ret = $this->_regist();
			if (FILE_FLG) {
				$fileRet = $this->_regist_file('img_receipt', sprintf('%05d', $this->entrySid));
			}
		}

		$output = array(
			'status' => 0
			// ,'file_status' => 0
		);
		if (FILE_FLG) {
			$output['file_status'] = 0;
		}

		if ($ret) {
			//成功時
			$output = array(
				'status' => 1
				// ,'file_status' => $fileRet ? 1:0
				,
				'cnt' => $this->getEntryCount(),
				'entrySid' => (string)$this->entrySid
				//,'entryDisplayId' => NULL
			);
			if (FILE_FLG) {
				$output['file_status'] = $fileRet ? 1 : 0;
			}
		} else {
		}
		//JSON出力
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($output);
	}

	/**
	 * カウント処理
	 */
	public function getCountData()
	{
		return $this->getEntryCount();
	}

	/**
	 * 20250529 mizukusa
	 * 応募ログ出力
	 */
	public function execLogOutput()
	{

		$output = array(
			'total_count' => $this->getEntryCount(),
			'total_category_count' => $this->getCategoryCount()
			// ,'daily_count_list' => $this->getDailyCountList()
			,
			'newest_arrivals_list' => $this->getNewestArrivalsList(3),
			'user_item_count_list' => $this->getItemCountList()
			// ,'user_devi_count_list' =>$this->getUserDeviCountList()
		);
		//JSON出力
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($output);
	}
	/**
	 * 20250529 mizukusa
	 * テーブルコメントの取得
	 */
	public function execOrderComment()
	{
		$table = DB_TABLE;
		$queryStr = "
		SELECT
			col.column_name,
			pgd.description
		FROM pg_catalog.pg_statio_all_tables AS st
		INNER JOIN pg_catalog.pg_description pgd ON pgd.objoid = st.relid
		INNER JOIN information_schema.columns col ON col.table_schema = st.schemaname
			AND col.table_name = st.relname
			AND col.ordinal_position = pgd.objsubid
		WHERE st.relname = '" . DB_TABLE . "'
		";
		$rows = array();
		$ret = $this->db->selectRecords($queryStr, NULL,  $rows);
		$comments = array();
		foreach ($rows as $kay => $val) {
			$comments[$val["column_name"]] = $val["description"];
		}
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($comments, true);
	}
	/**
	 * 受注状況情報出力
	 */
	public function execOrderList()
	{
		$rows = array();
		$arrSearch = array();
		$queryStr = "SELECT receipt_num  , entry_ts , c_item , order_number ,user_id  FROM t_ellena_cataloggift2507 
						WHERE is_del = 0  AND c_type = 'catalog' ";
		$ret = $this->db->selectRecords($queryStr, $arrSearch, $rows);
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	}

	/**
	 * DB登録処理
	 */
	private function _regist()
	{
		$ret = false;

		$arrPost = $_POST;

		$param = array(
			'entry_sid' => $this->entrySid
			//,'entry_display_id' => $this->entryDisplayId
			,
			'entry_ts' => $this->entryTs->format('Y-m-d H:i:s'),
			'user_devi_type' => $this->covNz($arrPost['user_devi_type'], 'unknown'),
			'regi_no' => $this->covNz($arrPost['regi_no'], ''),
			'receipt_no' => $this->covNz($arrPost['receipt_no'], ''),
			'receipt_num' => $this->covNz($arrPost['receipt_num'], ''),
			'c_type' => $this->covNz($arrPost['c_type'], ''),
			'c_id' => $this->covNz($arrPost['c_id'], ''),
			'entry_place' => $this->covNz($arrPost['entry_place'], ''),
			'staff_id' => $this->covNz($arrPost['staff_id'], '')
			// ,'c_tf' => $this->covNz($arrPost['c_tf'], '')
			,
			'c_item' => $this->covNz($arrPost['c_item'], ''),
			'c_item_jan' => $this->covNz($arrPost['c_item_jan'], ''),
			'deliv_date' => $this->covNz($arrPost['deliv_date'], ''),
			'user_id' => $this->covNz($arrPost['user_id'], ''),
			'name_sei' => $this->covNz($arrPost['name_sei'], ''),
			'name_mei' => $this->covNz($arrPost['name_mei'], ''),
			'kana_sei' => $this->covNz($arrPost['kana_sei'], ''),
			'kana_mei' => $this->covNz($arrPost['kana_mei'], ''),
			'zipcode' => $this->covNz($arrPost['zipcode'], ''),
			'pref_name' => $this->covNz($arrPost['pref_name'], ''),
			'address1' => $this->covNz($arrPost['address1'], ''),
			'address2' => $this->covNz($arrPost['address2'], ''),
			'telphone' => $this->covNz($arrPost['telphone'], ''),
			'sub_name_sei' => $this->covNz($arrPost['sub_name_sei'], ''),
			'sub_name_mei' => $this->covNz($arrPost['sub_name_mei'], ''),
			'sub_kana_sei' => $this->covNz($arrPost['sub_kana_sei'], ''),
			'sub_kana_mei' => $this->covNz($arrPost['sub_kana_mei'], ''),
			'sub_zipcode' => $this->covNz($arrPost['sub_zipcode'], ''),
			'sub_pref_name' => $this->covNz($arrPost['sub_pref_name'], ''),
			'sub_address1' => $this->covNz($arrPost['sub_address1'], ''),
			'sub_address2' => $this->covNz($arrPost['sub_address2'], ''),
			'sub_telphone' => $this->covNz($arrPost['sub_telphone'], ''),
			'order_number' => $this->covNz($arrPost['item_c_num'], '1')

			// ,'pack' => $this->covNz($arrPost['pack'], '')
			,
			'noshi' => $this->covNz($arrPost['noshi'], ''),
			'noshi_type' => $this->covNz($arrPost['noshi_type'], ''),
			'noshi_name' => $this->covNz($arrPost['noshi_name'], ''),
			'email' => $this->covNz($arrPost['email'], ''),
			'reg_ts' => 'now()',
			'reg_ope' => $this->dbOpeName,
			'mod_ts' => 'now()',
			'mod_ope' => $this->dbOpeName
		);

		// var_dump($param);
		// exit();
		//DBに登録
		$ret = false;
		$ret = $this->db->insert(DB_TABLE, $param);
		if ($ret) {
			return true;
		}
		return false;
	}


	/**
	 * ファイル登録処理
	 */
	private function _regist_file($nemekey, $fileBaseName)
	{
		$file = NULL;
		if (!is_null($this->fileRegistPath)) {
			$arrPost = $_POST;
			if (isset($arrPost['_files'])) {
				if (isset($arrPost['_files'][$nemekey])) {
					$file = $arrPost['_files'][$nemekey];
				}
			}
		}

		if (!is_null($file)) {
			//190304fuki
			foreach ($file as $key => $val) {
				$filePath = $this->fileRegistPath . $fileBaseName . '-' . $file[$key]['upload_file_name'];
				file_put_contents($filePath, $file[$key]['file_raw_data']);
				chmod($filePath, 0644);
			}
			// $filePath = $this->fileRegistPath.$fileBaseName.'-'.$file['upload_file_name'];
			// file_put_contents($filePath, $file['file_raw_data']);
			// chmod($filePath, 0644);
		}

		if (file_exists($filePath)) {
			return true;
		}
		return false;
	}

	/**
	 * 次のシーケンス取得
	 */
	private function getNextVal()
	{
		return $this->db->nextval(DB_TABLE_SEQ);
	}


	/**
	 * ■ ID正誤チェック処理 ※選べるギフト
	 */
	public function apiIdCheck()
	{
		$type = isset($_POST['c_type']) ? $_POST['c_type'] : '';
		$type = strtoupper($type);
		$id = isset($_POST['c_id']) ? $_POST['c_id'] : '';

		if ($type === '' || $id === '') {
			$this->outputJson(array('result' => false, 'comment' => 'no_post'));
			return;
		}

		$queryStr = 'select entry_sid from ' . DB_ID_TABLE;
		$queryStr .= ' where is_del = 0';
		$queryStr .= ' and c_type = ' . "'" . $type . "'";
		$queryStr .= ' and c_id = ' . "'" . $id . "'";
		// error_log($queryStr.PHP_EOL,3,'./log.txt');
		$ret = $this->db->selectRecords($queryStr, NULL, $rows);
		// error_log(json_encode($rows).PHP_EOL,3,'./log.txt');

		if (!$rows) {
			//存在しない場合
			$this->outputJson(array('result' => false, 'comment' => 'no_data'));
			return;
		}
		//存在する場合
		$this->outputJson(array('result' => true, 'comment' => 'regist_data'));
		return;
	}

	/**
	 * ■ ID正誤チェック処理 ※カタログギフト
	 */
	// public function apiIdCheck1(){
	// 	$type = isset($_POST['item']) ? $_POST['item']:'';
	// 	$id = isset($_POST['c_id']) ? $_POST['c_id']:'';
	// 	// error_log($type.PHP_EOL,3,'./log.txt');
	// 	// error_log($id.PHP_EOL,3,'./log2.txt');

	// 	if($type === '' || $id === ''){
	// 		$this->outputJson(array('result'=>false,'comment'=>'no_post'));
	// 		return;
	// 	}

	// 	$queryStr = 'select entry_sid from '.DB_ID_TABLE;
	// 	$queryStr .= ' where is_del = 0';
	// 	$queryStr .= ' and c_type = '."'".$type."'";
	// 	$queryStr .= ' and c_id = '."'".$id."'";
	// 	// error_log(json_encode($queryStr).PHP_EOL,3,'./log.txt');
	// 	$ret = $this->db->selectRecords($queryStr, NULL, $rows);
	// 	// error_log(json_encode($rows[0]['c_type']).PHP_EOL,3,'./log.txt');


	// 	if(!$rows){
	// 		//存在しない場合
	// 		$this->outputJson(array('result'=>false,'comment'=>'no_data'));
	// 		return;
	// 	}
	// 	//存在する場合
	// 	$this->outputJson(array('result'=>true,'comment'=>'regist_data'));
	// 	return;
	// }

	/**
	 * ■ 使用済IDチェック処理
	 */
	public function apiUsedIdCheck()
	{
		$type = isset($_POST['c_type']) ? $_POST['c_type'] : '';
		$id = isset($_POST['c_id']) ? $_POST['c_id'] : '';

		if ($type === '' || $id === '') {
			$this->outputJson(array('result' => false, 'comment' => 'no_post'));
			return;
		}

		$queryStr = 'select entry_sid from ' . DB_TABLE;
		$queryStr .= ' where is_del = 0';
		$queryStr .= ' and c_type = ' . "'" . $type . "'";
		$queryStr .= ' and c_id = ' . "'" . $id . "'";
		$ret = $this->db->selectRecords($queryStr, NULL, $rows);

		if (!$rows) {
			//存在しない場合
			$this->outputJson(array('result' => false, 'comment' => 'no_data'));
			return;
		}
		//存在する場合
		$this->outputJson(array('result' => true, 'comment' => 'regist_data'));
		return;
	}

	/**
	 * ■ ID・商品判定用
	 */
	// public function apiItemCheck(){
	// 	$id = isset($_POST['c_id']) ? $_POST['c_id']:'';
	// 	// error_log($type.PHP_EOL,3,'./log.txt');
	// 	// error_log($id.PHP_EOL,3,'./log2.txt');

	// 	if($id === ''){
	// 		$this->outputJson(array('result'=>false,'comment'=>'no_post'));
	// 		return;
	// 	}

	// 	$queryStr = 'select c_type from '.DB_ID_TABLE;
	// 	$queryStr .= ' where is_del = 0';
	// 	$queryStr .= ' and c_id = '."'".$id."'";
	// 	// error_log(json_encode($queryStr).PHP_EOL,3,'./log.txt');
	// 	$ret = $this->db->selectRecords($queryStr, NULL, $rows);
	// 	// error_log(json_encode($rows[0]['c_type']).PHP_EOL,3,'./log.txt');


	// 	if(!$rows){
	// 		//存在しない場合
	// 		$this->outputJson(array('result'=>false,'comment'=>'no_data'));
	// 		return;
	// 	}
	// 	//存在する場合
	// 	$this->outputJson(array('result'=>true,'item'=>"'".$rows[0]['c_type']."'",'comment'=>'regist_data'));
	// 	return;
	// }

	/**
	 * ■ JSON出力
	 */
	public function outputJson($result)
	{
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($result, true);
	}

	/**
	 * 応募数取得
	 */
	private function getEntryCount()
	{
		$cnt = -1;

		$queryStr = 'select';
		$queryStr .= '  count(entry_sid) as cnt';
		$queryStr .= ' from ' . DB_TABLE . ' ';
		$queryStr .= 'where is_del = 0';
		$ret = $this->db->selectRecords($queryStr, NULL, $rows);

		if (isset($rows[0]['cnt'])) {
			$cnt = $rows[0]['cnt'];
		}
		return $cnt;
	}

	/**
	 * Nz関数
	 */
	private function covNz($val, $alter)
	{
		if (isset($val) && $val != NULL) {
			return $val;
		} else {
			return $alter;
		}
	}

	/**
	 * タイムスタンプ変換
	 */
	private function covTimeStamp($strDateTime)
	{
		$ts = new DateTime($strDateTime);
		return $ts->format('Y-m-d H:i:s');
	}

	private function getCategoryCount()
	{
		$cnt = -1;

		$queryStr = 'select';
		$queryStr .= ' case';
		$queryStr .= ' when c_type like \'himawari\' THEN \'himawari\'';
		$queryStr .= ' when c_type like \'ajisai\' THEN \'ajisai\'';
		$queryStr .= ' else reg_ope';
		$queryStr .= ' end as Categorys,';
		$queryStr .= ' count(*)';
		$queryStr .= ' from ' . DB_TABLE;
		$queryStr .= ' where is_del = 0';
		$queryStr .= ' group by Categorys';

		$ret = $this->db->selectRecords($queryStr, NULL, $rows);
		// error_log(json_encode($rows).PHP_EOL,3,'./log.txt');

		if (count($rows) > 0) {
			foreach ($rows as $val) {
				if ($val['categorys'] === 'himawari' || $val['categorys'] === 'ajisai') {
					$res[$val['categorys']] = $val['count'];
				} else {
					$res['no'] = $val['count'];
				}
			}
			// error_log(json_encode($res).PHP_EOL,3,'./log.txt');
			return $res;
		}
		return $rows;
	}

	/**
	 * 日次集計取得
	 */
	private function getDailyCountList()
	{
		$rows = array();

		$queryStr  = "select ";
		$queryStr .= "   (to_char(entry_ts,'YYYYMMDD')) as key";
		$queryStr .= "  ,(to_char(entry_ts,'YYYY/MM/DD')) as date";

		$queryStr .= "  ,count(1)";

		$queryStr .= "from " . DB_TABLE . " ";
		$queryStr .= "where ";
		$queryStr .= "  is_del = 0 ";
		$queryStr .= "group by key,date ";
		$queryStr .= "order by key asc ";

		$ret = $this->db->selectRecords($queryStr, NULL, $rows);

		return $rows;
	}

	/**
	 * 直近数件分取得
	 * ※個人情報抜き
	 */
	private function getNewestArrivalsList($limit = 5)
	{
		$rows = array();
		$arrSearch = array();

		$queryStr  = "select";
		$queryStr .= "  entry_sid ";
		$queryStr .= " ,entry_ts ";
		$queryStr .= " ,(to_char(entry_ts,'YYYY-MM-DD')) as days ";
		$queryStr .= " ,(to_char(entry_ts,'HH24:MI')) as times ";
		$queryStr .= " ,user_devi_type ";
		$queryStr .= " ,c_type ";
		$queryStr .= " ,receipt_num ";
		$queryStr .= " ,c_item ";
		// $queryStr .= " ,c_tf ";
		$queryStr .= " ,deliv_date ";


		$queryStr .= " from " . DB_TABLE;
		$queryStr .= " where is_del = 0 ";
		$queryStr .= " order by entry_ts asc ";
		// $queryStr .= " order by entry_ts desc ";
		// $queryStr .= "order by entry_ts asc ";
		// $queryStr .= "limit ? ";

		// array_push($arrSearch, $limit);

		$ret = $this->db->selectRecords($queryStr, $arrSearch, $rows);
		// error_log(json_encode($rows).PHP_EOL,3,'./log.txt');
		return $rows;
	}

	/**
	 *
	 */
	private function getItemCountList()
	{
		$rows = array();

		$queryStr  = "select ";
		$queryStr .= " c_item ";
		$queryStr .= " ,count(c_item) ";

		$queryStr .= "from " . DB_TABLE . " ";
		$queryStr .= "where ";
		$queryStr .= "is_del = 0 ";
		$queryStr .= "group by c_item ";
		$queryStr .= "order by c_item asc ";

		$ret = $this->db->selectRecords($queryStr, NULL, $rows);

		return $rows;
	}

	/**
	 * デバイス集計取得
	 */
	private function getUserDeviCountList()
	{
		$rows = array();

		$queryStr  = "select ";
		$queryStr .= "   user_devi_type ";
		$queryStr .= "  ,count(1) as cnt ";

		$queryStr .= "from " . DB_TABLE . " ";
		$queryStr .= "where ";
		$queryStr .= "  is_del = 0 ";
		$queryStr .= "group by user_devi_type ";
		$queryStr .= "order by user_devi_type asc ";

		$ret = $this->db->selectRecords($queryStr, NULL, $rows);

		return $rows;
	}

	private function getTFCnt()
	{
		$cnt = -1;

		$queryStr = 'select';
		$queryStr .= ' case';
		$queryStr .= ' when c_tf like \'正%\' THEN \'t\'';
		$queryStr .= ' when c_tf like \'誤%\' THEN \'f\'';
		$queryStr .= ' else c_tf';
		$queryStr .= ' end as name,';
		$queryStr .= ' count(c_tf)';
		$queryStr .= ' from ' . DB_TABLE;
		$queryStr .= ' where is_del = 0';
		$queryStr .= ' group by c_tf';

		$ret = $this->db->selectRecords($queryStr, NULL, $rows);

		if (count($rows) > 0) {
			foreach ($rows as $val) {
				if ($val['name'] !== '') {
					$res[$val['name']] = $val['count'];
				} else {
					$res['no'] = $val['count'];
				}
			}
			return $res;
		}
		return $rows;
	}

	/**
	 * 募集終了時テーブルコメント自動上書き
	 */
	public function setClosedTableComment()
	{
		$strMarking = '【〆】';
		$targetSchemName  = 'public';
		$targetTableName = DB_TABLE;

		$arrSearch = array();

		$queryStr = 'select';
		$queryStr .= '  psut.relname as table_name ';
		$queryStr .= ' ,pd.description as table_comment ';
		$queryStr .= ' ,pd.objoid ';
		$queryStr .= 'from pg_stat_user_tables as psut ';
		$queryStr .= ' left join pg_description as pd ';
		$queryStr .= '  on psut.relid=pd.objoid and pd.objsubid = 0  ';

		$queryStr .= 'where pd.objoid is not null';
		$queryStr .= ' and psut.schemaname = ? and psut.relname = ? ';
		$queryStr .= ' and pd.description not like ? ';
		$queryStr .= 'limit 1 ';
		//
		array_push($arrSearch, $targetSchemName);
		array_push($arrSearch, $targetTableName);
		array_push($arrSearch, '%' . $strMarking . '%');

		$ret = $this->db->selectRecords($queryStr, $arrSearch, $rows);

		if (isset($rows[0]['table_comment'])) {
			$table_comment = $rows[0]['table_comment'];
			//コメント更新
			$queryCommentStr  = 'COMMENT ON TABLE ' . $targetSchemName . '.' . $targetTableName . ' IS ';
			$queryCommentStr .= "'" . $strMarking . $table_comment . "'";
			$this->db->selectRecords($queryCommentStr, NULL, $rows2);
		}
	}
}
