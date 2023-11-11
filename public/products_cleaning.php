<?php 

require_once('../vendor/autoload.php');

$config = array(
    'ShopUrl' => 'c1c87c.myshopify.com',
    'AccessToken' => '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',
);

PHPShopify\ShopifySDK::config($config);

$shopify = new PHPShopify\ShopifySDK;

$products = $shopify->Product->get();

if ($products === false) {
    echo "Failed to fetch products from Shopify.\n";
} else {
    $cleanedProducts = cleanProducts($products);
    echo json_encode($cleanedProducts, JSON_PRETTY_PRINT);
}

function cleanProducts($products) {
    
    foreach ($products as &$product) {
        $product = array_filter($product, function ($value) {
            return !in_array($value, ['N/A', '-', '', null], true);
        });
    
        // Modify the product title if N/A, -, or empty strings or Null are found in an array
        array_walk_recursive($product, function (&$value) use (&$product) {
            if (in_array($value, ['N/A', '-', '', null], true)) {
                if (isset($product['title']) && strpos($product['title'], 'nullable') === false) {
                    $product['title'] .= ' nullable';
                }
                $value = 'nullable';
            }
        });

        //bounce point update product quantity 
        if (isset($product['variants']) && is_array($product['variants'])) {
            foreach ($product['variants'] as &$variant) {
                if (isset($variant['inventory_quantity'])) {
                    $variantId = $variant['id'];
                    $newQuantity = 50;
                    $variant['inventory_quantity'] = 50;
                    // Use the Shopify SDK to update the inventory quantity
                    $shopify = new PHPShopify\ShopifySDK;
                    $updateVariant = $shopify->ProductVariant($variantId)->put(['inventory_quantity' => $newQuantity]);

                    if ($updateVariant === false) {
                        echo "Failed to update inventory quantity for variant ID $variantId.\n";
                    } else {
                        echo "Inventory quantity for variant ID $variantId updated successfully.\n";
                    }
                }
            }

        }


    }

    return $products;
}




























?>