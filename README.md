# R2 WC Product Notify 外掛介紹

>一句話說明：自動根據下單商品的課程日期進行 email 通知，適用於課程、門票等與時間有關的商品

## 使用方法

- 啟用外掛之後會自動新增一個類型為日期的課程日期的商品屬性
<img src="https://github.com/s0985514623/r2-wc-product-notify/assets/35906564/c9d2ddda-0de8-45e0-aa33-559a80f5fa44">

- 網站的時區記得要設定台北，不然 cron 的時間會有問題
<img src="https://github.com/s0985514623/r2-wc-product-notify/assets/35906564/859379cb-39b8-4a84-b6ac-1994e8f3515d">

- 左側選單會有"課前提醒通知設定"頁籤，預設提前 1 天的 10 點發信，如果小於當天的時間會馬上發信
<img src="https://github.com/s0985514623/r2-wc-product-notify/assets/35906564/75b12163-c3da-47f2-b317-5cae9a18a9ef">

- 於商品屬性點擊新增日期採用 DatePicker 方便選擇，選擇完畢自動帶入到商品屬性中
<img src="https://github.com/s0985514623/r2-wc-product-notify/assets/35906564/d54fbc57-5d87-448c-922d-270a0f89b883">

- 可變商品增加4個自訂欄位：表單連結、表單備註、時間、地點，會自動帶入到 email 內容中
<img src="https://github.com/s0985514623/r2-wc-product-notify/assets/35906564/d800f3a1-bde4-4c11-8bb7-c527e1a0181c">

- 除可變商品屬性以外也可針對商品進行全局設定
<img src="https://github.com/s0985514623/r2-wc-product-notify/assets/35906564/8c526482-0d30-4c8f-be79-413c5da03f7c">


## 課前提醒郵件範例
<img src="https://github.com/s0985514623/r2-wc-product-notify/assets/35906564/e3acad3c-bcbe-464d-9675-c845fb8ce241">
可以提前多少天以及在什麼幾點進行發送，比如一門課有 8/25 號 8/29 號兩個時段，客戶下單了 29 號這個時段的課程商品，後台設定了提前 2 天並且在 10 點進行發送，那麼客戶就會在 8/27 的早上 10 點收到通知信

## 電子郵件模板來源：Email Customizer for WooCommerce (Pro)套件
於該外掛調整好版型以後輸出PHP檔案，再將檔案放置於本外掛指定目錄/templates/email

## 未來可能開發功能
1. 自動帶入Email模板

## 參考
- 1.React腳手架來源 [J7](https://github.com/j7-dev/wp-react-plugin)
- 2.外掛開發參考 [J7](https://github.com/j7-dev/wp-power-shop)