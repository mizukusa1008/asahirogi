<?php
//UA確認・判定用クラス
class formModel
{

	//ダイジェスト認証
	function digestLogin()
	{
		if (!isset($_SERVER['PHP_AUTH_USER'])) {
			header('WWW-Authenticate: Basic realm="Private Page"');
			header('HTTP/1.0 401 Unauthorized');

			die('このページを見るにはログインが必要です');
		} else {

			if ($_SERVER['PHP_AUTH_USER'] != LOGVIEWER_USER || ($_SERVER['PHP_AUTH_PW']) != LOGVIEWER_PW) {
				// if ($_SERVER['PHP_AUTH_USER'] != LOGVIEWER_USER || md5($_SERVER['PHP_AUTH_PW']) != LOGVIEWER_PW){
				header('WWW-Authenticate: Basic realm="Private Page"');
				header('HTTP/1.0 401 Unauthorized');
				die('このページを見るにはログインが必要です');
			}
		}
	}

	//パラメータ
	function prmCheck($path, $paramArr = array())
	{
		if (PRM_FLG) {
			// if(defined(CP_PRM)){
			if (isset($_GET[GET_KEY])) {
				array_push($paramArr, GET_KEY . '=' . CP_PRM);
			}
			if (isset($_GET[GET_ID])) {
				array_push($paramArr, GET_ID . '=' . $_GET[GET_ID]);
			}
			if (isset($_GET[GET_ITEM])) {
				array_push($paramArr, GET_ITEM . '=' . $_GET[GET_ITEM]);
			}
			if (isset($_GET[GET_USER])) {
				array_push($paramArr, GET_USER . '=' . $_GET[GET_USER]);
			}
			if (isset($_GET[GET_QR])) {
				array_push($paramArr, GET_QR . '=' . $_GET[GET_QR]);
			}
		}
		if (count($paramArr) > 0) {
			return $path . '?' . join('&', $paramArr);
		}
		return $path;
	}

	//日付表記の取得
	function getDispPeriod($date)
	{
		$period = new DateTime($date);
		$week = array('日', '月', '火', '水', '木', '金', '土');
		$period = $period->format('Y年n月j日') . '(' . $week[$period->format('w')] . ')';
		return $period;
	}

	/* file_get_contentsのラッパー関数
	 * @pram $apiUrl APIのURL
	 * @pram $pram 送信パラメータ配列 例：array('method' => 'post','t' => 'keys_genre')
	 * @pram $method GET or POST
	 */
	function asfileGetContents($apiUrl, $pram)
	{
		$method = 'POST';
		$context = stream_context_create(array(
			'http' => array(
				'method' => $method,
				'header' => 'Content-Type: application/x-www-form-urlencoded',
				//'proxy' => 'tcp://127.0.0.1:80',
				'ignore_errors' => true,
				'content' => http_build_query($pram, "", "&")
			)
		));
		$res = file_get_contents($apiUrl, false, $context);

		return $res;
	}
	//csv取得改修　最初の行をヘッダ行として読み込む
	public function getCsvFile($csvfile)
	{
		if (!file_exists($csvfile)) return false;
		if (($handle = fopen($csvfile, "r")) !== FALSE) {
			if (($headers = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$primary_colum=0;
				while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
					foreach($headers as $key => $value){
						$d[$value]=$row[$key];
						
					}
					$data[$d[$headers[$primary_colum]]]=$d;
				}
			}
			fclose($handle);
		}
		return $data;
	}

	/*************************************************
	 * csv取得 読み込みCSVはUTF-8であること
	 * https://kantaro-cgi.com/blog/php/super-csv-loader.html
	 *************************************************/
	public function getCsv($csvfile, $keyArr = [])
	{
		// ファイル存在確認
		if (!file_exists($csvfile)) return false;

		// SplFileObject()を使用してCSVロード
		$file = new SplFileObject($csvfile);
		$file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::READ_AHEAD);

		// 各行を処理
		$records = [];
		$colbook = [];
		foreach ($file as $i => $row) {
			if (count($keyArr) > 0) {
				//単純な配列化
				$line = [];
				// foreach($keyArr as $j=>$col){
				// 	if($i === 0 && $j === 0){
				// 		$line[$keyArr[$j]] = str_replace(array("\r\n","\r","\n"," ",'﻿﻿',PHP_EOL),'',@$row[$j]);
				// 	}else{
				// 		$line[$keyArr[$j]] = @$row[$j];
				// 	}
				// }

				// // foreach($keyArr as $j=>$col){
				// // 	$line[$keyArr[$j]] = str_replace(array("\r\n","\r","\n"), "", @$row[$j]);
				// // }
				foreach ($keyArr as $j => $col) $line[$keyArr[$j]] = @$row[$j];
				$records[] = $line;
			} else {
				// 1行目はキーヘッダ行として取り込み
				if ($i === 0) {
					foreach ($row as $j => $col) $colbook[$j] = $col;
					continue;
				}
				// 2行目以降はデータ行として取り込み
				$line = [];
				foreach ($colbook as $j => $col) $line[$colbook[$j]] = @$row[$j];
				$records[] = $line;
			}
		}

		return $records;
	}

	/* ヘッダーをテキストから画像へ切り替え
	 * img_title_$pathParts.jpg を表示する。
	 * ただし、受付前、終了後ページの場合は img_title_default.jpgとする
	 */
	function createHeadImg()
	{
		$pathParts = pathinfo($_SERVER["REQUEST_URI"], PATHINFO_FILENAME);
		if ($pathParts === 'before' || $pathParts === 'closed') {
			$pathParts = 'default';
		}
		$pathParts = 'default';

		// $res = '<div class="header_img_wrap">'.PHP_EOL;
		// $res .= '<img src="img/title_img_'.$pathParts.'.png" />'.PHP_EOL;
		// $res .= '</div>'.PHP_EOL;

		$res = '<div class="block_img_wrap">' . PHP_EOL;
		// $res .= '<div class="block block_header">'.PHP_EOL;
		$res .= '<div class="header_img_wrap ' . $_GET[GET_KEY] . '">' . PHP_EOL;
		if ($_GET[GET_KEY] == "catalog") {
			$res .= '<img src="img/head_' . $_GET[GET_KEY] . '.png?v1" />' . PHP_EOL;
		} else {
			$res .= '<img src="img/head_' . $_GET[GET_KEY] . '.svg?v1" />' . PHP_EOL;
		}

		// $res .= '<img src="img/title_img_'.$pathParts.'.jpg" />'.PHP_EOL;
		$res .= '</div></div>' . PHP_EOL;
		return $res;
	}
}
