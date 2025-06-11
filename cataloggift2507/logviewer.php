<?php
//初期処理
require_once('include/config.php');
require_once('include/config_log.php');
require_once(DIR_INCLUDE.'/model.php');
require_once(DIR_INCLUDE.'/templateContainer.php');

class pageLogviewer{

	private $tmpl;
	private $model;

	const API_LOGOUTPUT_URL = API_DATA_LOGOUTPUT_URL;

	//コンストラクタ
	function __construct(){
		// class読み込み
		$this->model = new formModel();
		//ダイジェスト認証
		$this->model->digestLogin();

		$this->tmpl = new templateContainer(basename(__FILE__, '.php'));
		$this->_assign();
		$this->tmpl->displayContent();
	}

	function _assign(){
		$dateNow = new DateTime();
		$dateBgn =  new DateTime(ENTRY_PERIOD_BGN);
		$dateEnd =  new DateTime(ENTRY_PERIOD_END);

		//API経由で情報登録
		$res = $this->model->asfileGetContents(self::API_LOGOUTPUT_URL, array());
		$arrLogOutput = json_decode($res);

		$total_count = 0;
		if(isset($arrLogOutput->total_count)){
			$total_count = $arrLogOutput->total_count;
		}

		$categoryList = [];
		if(isset($arrLogOutput->total_category_count)){
			$total_count2 = $arrLogOutput->total_category_count;
			if(count($total_count2) > 0){
				//オブジェクト→配列
				foreach ($total_count2 as $key => &$val) {
					$categoryList[$key] = $key.':'.$val.'件';
					// $categoryList[$key] = '<b>'.$key.':'.$val.'件</b>';
				}
			}
			$categoryList = array_reverse($categoryList);
			// var_dump($categoryList);
		}

		//新着応募
		$html = '';
		$html1 = '';
		$arrNewestArrivalsList = array();
		if(isset($arrLogOutput->newest_arrivals_list)){
			//$arrList = array();
			$arr = $arrLogOutput->newest_arrivals_list;

			if(count($arr) > 0){
				//オブジェクト→配列
				foreach ($arr as $key => &$val) {
					$arrList[$key] = (array)$val;
				}
				unset($val);
				// $html = $this->createTable($arrList);
				$html = $this->createTables($arrList);
			}
			$arrNewestArrivalsList = $arrList;
		}
		// $this->tmpl->contents->addRows('_row_newest', $arrNewestArrivalsList);
		// var_dump($arrNewestArrivalsList);

		$itemTotalHtml = '';
		// $arrPresentTypeCountList = array();
		if(isset($arrLogOutput->user_item_count_list)){
			$itemTotalList = array();
			$arr = $arrLogOutput->user_item_count_list;

			if(count($arr) > 0){
				//オブジェクト→配列
				foreach ($arr as $key => &$val) {
					$itemTotalList[$key] = (array)$val;
				}
				unset($val);
				$itemTotalHtml = $this->createTableItemTotal($itemTotalList);
				// var_dump($itemTotalHtml);
			}

		}

		//値挿入
		$this->tmpl->contents->addVars('_contents', array(
			 'TOTAL_COUNT' => $arrLogOutput->total_count
			,'CATEGORY_COUNT' => implode(' ／ ',$categoryList)
			,'NOW_TXT' => $dateNow->format('Y年n月j日 H:i')
			,'PERIOD' => $dateBgn->format('Y年n月j日').'～'.$dateEnd->format('Y年n月j日')
			,'PERIOD_TXT' => $dateEnd->format('Y年n月j日 H:i:s').' まで'
			,'HIMAWARI_TOTAL_HTML' => $itemTotalHtml[0]
			,'AJISAI_TOTAL_HTML' => $itemTotalHtml[1]
			,'CATALOG_TOTAL_HTML' => $itemTotalHtml[2]
			,'TABLE_HTML' => $html
		));

	}

	//テーブル生成
	function createTables($arr){
		$res = '';
		$t2 = "\t\t";
		$t3 = "\t\t\t";
		foreach($arr as $key => $val){
			$resSub = '';
			$resSub .= $t2.'<tr class="row '.$val['c_type'].'">'.PHP_EOL;
			$resSub .= $t3.'<td>'.($key+1).'</td>'.PHP_EOL;

			foreach($val as $subKey => $subVal){
				switch($subKey){
					case 'days':
						$resSub .= $t3.'<td>'.$subVal;
					break;
					case 'times':
						$resSub .= ' '.$subVal.'</td>'.PHP_EOL;
					break;
					case 'c_type':
						$resSub .= $t3.'<td><span>'.$subVal.'</span></td>'.PHP_EOL;
					break;
					case 'receipt_num':
					case 'c_item':
					case 'c_tf':
					case 'deliv_date':
						$resSub .= $t3.'<td>'.$subVal.'</td>'.PHP_EOL;
					break;
					default:
					break;
				}
			}
			$resSub .= $t2.'</tr>'.PHP_EOL;
			$res = $resSub.$res;
		}
		return $res;
	}

	//テーブル生成 賞品番号別
	function createTableItemTotal($arr){
		$itemArr = unserialize(ITEM_ALL_ARR);
		// var_dump($arr);
		$dataArr = [];
		if(isset($arr) && count($arr) > 0){
			foreach($arr as $arrVal){
				$dataArr[$arrVal['c_item']] = $arrVal['count'];
			}
		}
		$htmlH = "";
		$htmlA = "";
		$htmlC ="";
		foreach($itemArr as $key => $val){
			foreach ($val as $subKey => $subVal) {
				$countVal = 0;
				if(isset($dataArr[$subKey])){ $countVal = $dataArr[$subKey]; }
				$html = "<tr>\n";
				$html .= "<th>[".$subKey."]".$subVal[0]."</th>\n";
				$html .= "<td>".$countVal."</td>\n";
				$html .= "</tr>\n";
				if($key === 'himawari'){
					$htmlH .= $html;
				}else if($key === 'yuzu'){
					$htmlA .= $html;
				}else{
					$htmlC .= $html;
				}
			}

		}
		return [$htmlH,$htmlC];


		// try{
		// 	//db値調整　存在しない場合の対応
		// 	$dataArr = array();
		// 	foreach($arr as $arrVal){
		// 		$dataArr[$arrVal['present_type']] = $arrVal['count'];
		// 	}
		//
		// 	$html = "";
		// 	foreach($preArr as $key => $val){
		// 		$countVal = 0;
		// 		if(isset($dataArr[$key])){ $countVal = $dataArr[$key]; }
		// 		$html .= "<tr>\n";
		// 		$html .= "<th>[".$key."]".$val['present']."</th>\n";
		// 		$html .= "<td>".$countVal."</td>\n";
		// 		$html .= "</tr>\n";
		// 	}
		// 	return $html;
		//
		// }catch(Exception $e){
		// }
	}

	// //範囲内の日付をキーとして配列として返す
	// function createArr(){
	// 	//日付
	// 	$formatStr = 'Y-m-d';
	// 	$dateNow = new DateTime();// 現在日時
	// 	$dateNowKey = $dateNow->format($formatStr);
	// 	$dateBgn = new DateTime(ENTRY_PERIOD_BGN);
	// 	$dateBgnKey = $dateBgn->format($formatStr);
	// 	$dateEnd = new DateTime(ENTRY_PERIOD_END);
	// 	$dateEndKey = $dateEnd->format($formatStr);
	// 	$arr = array();
	// 	//開始日チェック
	// 	$currentDate = $dateNowKey < $dateBgnKey ? clone $dateNow : clone $dateBgn;
	// 	$currentDateKey = $dateNowKey < $dateBgnKey ? $dateNowKey : $dateBgnKey;
	// 	//範囲内の日付をキーとして配列化
	// 	while ($currentDateKey <= $dateEndKey && $currentDateKey <= $dateNowKey) {
	// 		$arr[$currentDateKey] = array();
	// 		$currentDate->modify('+1 day');//1日進める
	// 		$currentDateKey = $currentDate->format($formatStr);
	// 	}
	// 	return $arr;
	// }

	// //テーブル生成
	// function createTable($arr){
	// 	//範囲内の日付をキーとして配列化
	// 	$dateArr = $this->createArr();
	// 	$maxCnt = count($arr);
	// 	$WhileCnt = 0;
	// 	$html = '';
	// 	try{
	// 		while ($maxCnt > $WhileCnt) {
	// 			$dateArr[$arr[$WhileCnt]['days']][] = $arr[$WhileCnt];
	// 			++$WhileCnt;
	// 		}
	// 		ksort($dateArr);
	// 		// var_dump($dateArr);
	// 		$cntNum = 1;
	// 		foreach($dateArr as $key => $valArr){
	// 			$dataCnt = count($valArr);
	// 			if(count($valArr) === 0){
	// 				$html = $this->createTableTr($cntNum,array(),$key,$dataCnt).$html;
	// 				++$cntNum;
	// 			}else{
	// 				$subHtml = '';
	// 				krsort($valArr);
	// 				foreach($valArr as $valArrKey => $varArrVal){
	// 					if($valArrKey === 0){
	// 						$subHtml = $this->createTableTr($cntNum,$varArrVal,$key,$dataCnt).$subHtml;
	// 					}else{
	// 						$subHtml = $this->createTableTr($cntNum,$varArrVal,$key,$dataCnt,false).$subHtml;
	// 					}
	// 					++$cntNum;
	// 				}
	// 				$html = $subHtml.$html;
	// 			}
	// 		}
	// 		return $html;
	// 	}catch(Exception $e){
	// 	}
	// }

	// //テーブル生成繰り返し部分
	// function createTableTr($no,$subArr,$days,$dataCnt,$firstFlg=true)
	// {
	// 	$rowspanCnt = $dataCnt === 0 ? 1 : $dataCnt;
	// 	$datetime = isset($subArr['days']) && isset($subArr['times']) ? '<span class="pc_only">'.$subArr['days'].' </span>'.$subArr['times'] : '';
	// 	$type = isset($subArr['user_devi_type']) ? $subArr['user_devi_type'] : '';
	//
	// 	$html = "";
	// 	$html .= "\t\t\t".'<tr class="row">'."\n";
	// 	$html .= "\t\t\t".'<td>'.$no.'</td>'."\n";
	// 	$html .= "\t\t\t".'<td>'.$datetime.'</td>'."\n";
	// 	$html .= "\t\t\t".'<td>'.$type.'</td>'."\n";
	// 	if($firstFlg){
	// 		$html .= "\t\t\t".'<td rowspan="'.$rowspanCnt.'">'.$days.'</td>'."\n";
	// 		$html .= "\t\t\t".'<td rowspan="'.$rowspanCnt.'">'.$dataCnt.'</td>'."\n";
	// 	}
	// 	$html .= "\t\t\t".'</tr>'."\n";
	//
	// 	return $html;
	// }

}

new pageLogviewer();//表示

?>
