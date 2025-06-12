/**
 * 管理画面用JavaScript
 */
document.addEventListener('DOMContentLoaded', function() {
    // APIエンドポイント取得 - PHPから渡された安全なエンドポイントを使用
    function getApiEndpoint() {
        // サーバーから提供されたAPIエンドポイントを使用
        return document.getElementById('api_endpoint').value;
    }
    
    // 初期設定
    const apiUrl = getApiEndpoint();
    let allData = [];
    let filteredData = [];
    let catalogItems = {};
    let shopItems = {};
    
    // CSV商品データ読み込み
    fetchCatalogItems();
    fetchShopItems();
    
    // APIからデータ読み込み
    fetchApiData();
    
    // 絞込みイベントリスナー
    document.getElementById('date_from').addEventListener('change', filterData);
    document.getElementById('date_to').addEventListener('change', filterData);
    document.getElementById('shop_id').addEventListener('change', filterData);
    
    // データ読み込み関数
    function fetchApiData() {
        fetch(apiUrl)
            .then(response => response.json())
            .then(data => {
                allData = data.data;
                document.getElementById('total_count').textContent = data.total_count;
                filterData(); // 初期表示用にフィルタリング実行
            })
            .catch(error => {
                console.error('APIデータの取得に失敗しました:', error);
                document.getElementById('error-message').textContent = 'データの取得に失敗しました';
            });
    }
    
    // 商品CSV読み込み
    function fetchCatalogItems() {
        fetch('include/choiceList/ellena_catalog_items.csv')
            .then(response => response.text())
            .then(csvData => {
                const lines = csvData.split('\n');
                lines.slice(1).forEach(line => { // ヘッダー行をスキップ
                    const cols = line.split(',');
                    if (cols.length >= 2) {
                        const id = cols[0].replace(/"/g, '').trim();
                        const name = cols[1].replace(/"/g, '').trim();
                        if (id && name) {
                            catalogItems[id] = name;
                        }
                    }
                });
            })
            .catch(error => console.error('商品CSVの読み込みに失敗しました:', error));
    }
    
    // 店舗CSV読み込み
    function fetchShopItems() {
        fetch('include/choiceList/ellena_shops.csv')
            .then(response => response.text())
            .then(csvData => {
                const lines = csvData.split('\n');
                lines.slice(1).forEach(line => { // ヘッダー行をスキップ
                    const cols = line.split(',');
                    if (cols.length >= 2) {
                        const id = cols[0].replace(/"/g, '').trim();
                        const name = cols[1].replace(/"/g, '').trim();
                        if (id && name) {
                            shopItems[id] = name;
                        }
                    }
                });
            })
            .catch(error => console.error('店舗CSVの読み込みに失敗しました:', error));
    }
    
    // データフィルタリング
    function filterData() {
        const dateFrom = document.getElementById('date_from').value;
        const dateTo = document.getElementById('date_to').value;
        const shopId = document.getElementById('shop_id').value;
        
        filteredData = allData.filter(item => {
            // 日付フィルタリング
            let dateMatch = true;
            const itemDate = item.entry_ts.split(' ')[0]; // YYYY-MM-DD部分を抽出
            
            if (dateFrom && itemDate < dateFrom) {
                dateMatch = false;
            }
            
            if (dateTo && itemDate > dateTo) {
                dateMatch = false;
            }
            
            // 店舗フィルタリング
            let shopMatch = true;
            if (shopId && item.user_id !== shopId) {
                shopMatch = false;
            }
            
            return dateMatch && shopMatch;
        });
        
        // 表示件数更新
        document.getElementById('filtered_count').textContent = filteredData.length;
        
        // テーブル更新
        renderTable();
    }
    
    // テーブル描画
    function renderTable() {
        const tableBody = document.getElementById('data_table_body');
        tableBody.innerHTML = '';
        
        if (filteredData.length === 0) {
            const row = document.createElement('tr');
            const cell = document.createElement('td');
            cell.colSpan = 6;
            cell.textContent = 'データが見つかりません';
            cell.classList.add('text-center');
            row.appendChild(cell);
            tableBody.appendChild(row);
            return;
        }
        
        filteredData.forEach((item, index) => {
            const row = document.createElement('tr');
            
            // #
            const indexCell = document.createElement('td');
            indexCell.textContent = index + 1;
            row.appendChild(indexCell);
            
            // 受付日時
            const dateCell = document.createElement('td');
            dateCell.textContent = formatDate(item.entry_ts);
            row.appendChild(dateCell);
            
            // 受付番号
            const receiptCell = document.createElement('td');
            receiptCell.textContent = item.receipt_num || '-';
            row.appendChild(receiptCell);
            
            // 店舗名
            const shopCell = document.createElement('td');
            shopCell.textContent = shopItems[item.user_id] || item.user_id || '-';
            row.appendChild(shopCell);
            
            // 商品コード
            const itemCodeCell = document.createElement('td');
            itemCodeCell.textContent = item.c_item || '-';
            row.appendChild(itemCodeCell);
            
            // 商品名
            const itemNameCell = document.createElement('td');
            itemNameCell.textContent = catalogItems[item.c_item] || '-';
            row.appendChild(itemNameCell);
            
            tableBody.appendChild(row);
        });
    }
    
    // 日付フォーマット YYYY-MM-DD HH:MM:SS -> YYYY/MM/DD HH:MM
    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        if (isNaN(date.getTime())) return dateStr;
        
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hour = String(date.getHours()).padStart(2, '0');
        const minute = String(date.getMinutes()).padStart(2, '0');
        
        return `${year}/${month}/${day} ${hour}:${minute}`;
    }
});