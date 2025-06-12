<?php
// APIエンドポイントルーティングスクリプト
// クライアント側からAPIサーバーへの中継点として機能し、実際のAPIサーバーアドレスを隠す

require_once('include/config.php');

// CORS対応（必要に応じて）
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// 実際のAPIエンドポイント
$target_api = '';

// 環境に応じてAPIルートを設定
global $folderName;
if (SITE_STAGE == 'prd') {
    // 本番環境: DBサーバーのAPIエンドポイント
    $target_api = API_ROOT_URL . $folderName . '/api_cataloggift2507/apiAdminData.php';
} else {
    // 開発環境: 相対パスでのAPI指定
    $target_api = '../api_cataloggift2507/apiAdminData.php';
}

// リクエストメソッドに応じた処理
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // GETリクエスト処理
    $response = file_get_contents($target_api);
    header('Content-Type: application/json');
    echo $response;
    exit;

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POSTリクエスト処理
    $post_data = file_get_contents('php://input');
    
    // cURLを使用してAPIサーバーにリクエストを転送
    $ch = curl_init($target_api);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // レスポンス送信
    http_response_code($status);
    header('Content-Type: application/json');
    echo $response;
    exit;
    
} else {
    // 未対応メソッド
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}
?>