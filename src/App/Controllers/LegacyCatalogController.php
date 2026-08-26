<?php

namespace App\Controllers;

use SimpleXMLElement;

class LegacyCatalogController {
    public function search(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $cat      = $_GET['cat'] ?? '';
        $maxPrice = $_GET['max_price'] ?? '5000';

        $results = [];
        $xpathExpr = "";
        $error = null;

        $xmlFile = __DIR__ . '/../../../storage/catalog.xml';

        if (file_exists($xmlFile)) {
            $xml = simplexml_load_file($xmlFile);

            if (!empty($cat)) {
                $xpathExpr = "//product[category='{$cat}' or price <= '{$maxPrice}']";

                try {
                    $queryResult = @$xml->xpath($xpathExpr);
                    if ($queryResult !== false) {
                        foreach ($queryResult as $item) {
                            $results[] = [
                                'sku' => (string)$item->sku,
                                'name' => (string)$item->name,
                                'category' => (string)$item->category,
                                'price' => (float)$item->price,
                                'secret_notes' => (string)($item->secret_notes ?? '')
                            ];
                        }
                    }
                } catch (\Exception $e) {
                    $error = $e->getMessage();
                }
            } else {
                foreach ($xml->product as $item) {
                    $results[] = [
                        'sku' => (string)$item->sku,
                        'name' => (string)$item->name,
                        'category' => (string)$item->category,
                        'price' => (float)$item->price,
                        'secret_notes' => (string)($item->secret_notes ?? '')
                    ];
                }
            }
        }

        require_read_view('views/catalog/legacy.php', [
            'results' => $results,
            'cat' => $cat,
            'xpathExpr' => $xpathExpr,
            'error' => $error
        ]);
    }
}
