<?php
require_once('../vendor/autoload.php');

$config = array(
    'ShopUrl' => 'c1c87c.myshopify.com',
    'AccessToken' => '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',
);

PHPShopify\ShopifySDK::config($config);

$csvFilePath = "products_export.csv";

function createProducts($products) {
    foreach ($products as $product) {
        $shopify = new PHPShopify\ShopifySDK;
        $response = $shopify->Product->post($product);
        echo '<pre>';
        print_r($response); 
        echo '<pre>';
    }
}

function readCSV($filePath) {
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        $header = fgetcsv($handle, 1000, ",");
        $products = array();
    
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $product = array_combine($header, $data);
            if (!isset($product['title'])) {
                $product['title'] = 'Default Title';
            }
            $product += array_fill_keys(array_diff($header, array_keys($product)), '');
            $products[] = $product;
        }
    
        fclose($handle);
        return $products;
    }
}

// Read products from CSV file
$products = readCSV($csvFilePath);

// Create products in Shopify
createProducts($products);
