/**
 * 管理画面用JavaScript
 */
document.addEventListener('DOMContentLoaded', function() {
    // ページ読み込み完了時の処理
    console.log('Admin page loaded');

    // 店舗選択の初期値設定
    const urlParams = new URLSearchParams(window.location.search);
    const shopId = urlParams.get('shop_id');
    if (shopId) {
        const shopSelect = document.getElementById('shop_id');
        if (shopSelect) {
            shopSelect.value = shopId;
        }
    }

    // 日付フィールドにデフォルト値を設定（検索条件がなければ）
    const dateFrom = urlParams.get('date_from');
    const dateTo = urlParams.get('date_to');
    
    if (!dateFrom && !dateTo) {
        // 現在の日付から1ヶ月前をデフォルトの開始日に
        const today = new Date();
        const oneMonthAgo = new Date();
        oneMonthAgo.setMonth(today.getMonth() - 1);
        
        const fromField = document.getElementById('date_from');
        const toField = document.getElementById('date_to');
        
        if (fromField && !fromField.value) {
            fromField.value = formatDate(oneMonthAgo);
        }
        
        if (toField && !toField.value) {
            toField.value = formatDate(today);
        }
    }
});

/**
 * 日付をYYYY-MM-DD形式に整形する
 * @param {Date} date - 日付オブジェクト
 * @returns {string} YYYY-MM-DD形式の文字列
 */
function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}