<?php

namespace App\Services;

use App\Models\Product;
use SimpleXMLElement;

class XmlImportService {
    public static function importProductsFromXml(string $xmlContent): array {
        libxml_disable_entity_loader(false);
        
        $imported = 0;
        $errors = [];

        try {
            $xml = @simplexml_load_string($xmlContent, 'SimpleXMLElement', LIBXML_NOENT | LIBXML_DTDLOAD);
            
            if ($xml === false) {
                return ['error' => 'XML inválido ou mal formatado.'];
            }

            foreach ($xml->product as $item) {
                $name = (string)$item->name;
                $price = (float)$item->price;
                $category = (string)($item->category ?? 'Importados');
                $sku = (string)($item->sku ?? ('IMP-' . rand(1000, 9999)));

                if (!empty($name) && $price > 0) {
                    Product::create([
                        'sku' => $sku,
                        'name' => $name,
                        'category' => $category,
                        'price' => $price,
                        'description' => (string)($item->description ?? 'Importado via catálogo XML'),
                        'cost_price' => $price * 0.6,
                        'stock' => 20
                    ]);
                    $imported++;
                }
            }

            return ['success' => true, 'imported_count' => $imported];

        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
