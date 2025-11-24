<?php
function parse_store_numeric($value): ?float
{
    if (is_numeric($value)) {
        return (float) $value;
    }

    if (is_string($value)) {
        $normalized = trim($value);
        if ($normalized === '') {
            return null;
        }

        $normalized = str_replace(['$', '€', ',', ' '], '', $normalized);
        $normalized = str_replace('%', '', $normalized);
        // Remove all dots except the last one to support values like "2.499.90".
        $normalized = preg_replace('/\.(?=.*\.)/', '', $normalized);

        if ($normalized === '' || !is_numeric($normalized)) {
            return null;
        }

        return (float) $normalized;
    }

    return null;
}

function load_store_config(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    $configPath = __DIR__ . '/../store_config.json';
    $cache = json_decode(@file_get_contents($configPath), true) ?: [];
    return $cache;
}

function load_store_inventory_index(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    $inventoryPath = __DIR__ . '/../inventory.json';
    $cache = json_decode(@file_get_contents($inventoryPath), true) ?: [];
    return $cache;
}

function merge_inventory(array $product): array
{
    $inventoryIndex = load_store_inventory_index();
    $defaults = $inventoryIndex['__defaults'] ?? [];
    $sku = $product['sku'] ?? null;
    $productInventory = $sku && isset($inventoryIndex[$sku]) ? $inventoryIndex[$sku] : [];

    $inventory = array_merge($defaults, $productInventory);
    $product['inventory'] = $inventory;

    return $product;
}

function enrich_store_product(array $product): array
{
    $type = $product['product_type'] ?? 'flooring';
    $product = merge_inventory($product);

    if (!isset($product['package_label']) || !isset($product['package_label_plural'])) {
        if ($type === 'molding') {
            $product['package_label'] = 'package';
            $product['package_label_plural'] = 'packages';
        } else {
            $product['package_label'] = 'box';
            $product['package_label_plural'] = 'boxes';
        }
    }

    if (!isset($product['measurement_unit'])) {
        $product['measurement_unit'] = $type === 'molding' ? 'lf' : 'sqft';
    }

    if (!isset($product['price_per_unit_base']) && isset($product['price_per_unit'])) {
        $product['price_per_unit_base'] = $product['price_per_unit'];
    }

    if ($type === 'molding') {
        $base = isset($product['price_per_unit_base']) ? (float) $product['price_per_unit_base'] : (isset($product['price_per_unit']) ? (float) $product['price_per_unit'] : null);
        if ($base !== null) {
            $loading = isset($product['price_loading']) ? (float) $product['price_loading'] : 0.0;
            $transport = isset($product['transport_and_logistic_price']) ? (float) $product['transport_and_logistic_price'] : 0.0;
            $percentage = isset($product['price_percentage']) ? (float) $product['price_percentage'] : 0.0;

            $first = $base + $loading + $transport;
            $second = $first * ($percentage / 100);
            $final = $first + $second;

            $product['computed_price_per_unit'] = round($final, 4);
            $product['price_per_unit'] = $product['computed_price_per_unit'];
        }
    } else {
        $truckLoad = array_key_exists('truck_load_1216_pallets', $product) ? parse_store_numeric($product['truck_load_1216_pallets']) : null;
        $loading = array_key_exists('precio_de_carga', $product) ? parse_store_numeric($product['precio_de_carga']) : null;
        $storage = array_key_exists('storage_por_sqft_depende_del_mes', $product) ? parse_store_numeric($product['storage_por_sqft_depende_del_mes']) : null;
        $labor = array_key_exists('transporte_mano_de_obra_por_sqft', $product) ? parse_store_numeric($product['transporte_mano_de_obra_por_sqft']) : null;
        $margin = array_key_exists('margen_final', $product) ? parse_store_numeric($product['margen_final']) : null;

        $stockComponents = [$truckLoad, $loading, $storage, $labor, $margin];
        $stockPrice = 0.0;
        $hasStockPrice = false;
        foreach ($stockComponents as $value) {
            if ($value === null) {
                continue;
            }
            $stockPrice += $value;
            $hasStockPrice = true;
        }

        if ($hasStockPrice) {
            $product['computed_price_per_unit_stock'] = round($stockPrice, 4);
            $product['computed_price_per_unit'] = $product['computed_price_per_unit_stock'];
            $product['price_per_unit'] = $product['computed_price_per_unit_stock'];
        }

        $backorderComponents = [$truckLoad, $loading, $margin];
        $backorderPrice = 0.0;
        $hasBackorderPrice = false;
        foreach ($backorderComponents as $value) {
            if ($value === null) {
                continue;
            }
            $backorderPrice += $value;
            $hasBackorderPrice = true;
        }

        if ($hasBackorderPrice) {
            $product['computed_price_per_unit_backorder'] = round($backorderPrice, 4);
        }

        if (!$hasStockPrice) {
            $existing = null;
            if (isset($product['computed_price_per_unit'])) {
                $existing = (float) $product['computed_price_per_unit'];
            } elseif (isset($product['price_per_unit'])) {
                $existing = (float) $product['price_per_unit'];
            } elseif (isset($product['price_sqft'])) {
                $existing = (float) $product['price_sqft'];
            }
            if ($existing !== null) {
                $product['computed_price_per_unit'] = $existing;
                $product['price_per_unit'] = $existing;
            }
        }
    }

    $coverage = $product['box_sf'] ?? $product['coverage_per_box'] ?? $product['sqft_per_box'] ?? null;
    $coverageValue = parse_store_numeric($coverage);
    if ($coverageValue === null || $coverageValue <= 0) {
        $length = isset($product['length_ft']) ? (float) $product['length_ft'] : null;
        $pieces = isset($product['pieces_per_box']) ? (float) $product['pieces_per_box'] : null;
        if ($length && $pieces) {
            $coverageValue = $length * $pieces;
        } else {
            $coverageValue = null;
        }
    }
    if ($coverageValue !== null) {
        $product['computed_coverage_per_package'] = (float) $coverageValue;
        if (!isset($product['coverage_per_box'])) {
            $product['coverage_per_box'] = $product['computed_coverage_per_package'];
        }
    }

    if (isset($product['computed_coverage_per_package'])) {
        if (isset($product['computed_price_per_unit'])) {
            $product['computed_price_per_package'] = $product['computed_price_per_unit'] * $product['computed_coverage_per_package'];
        }
        if (isset($product['computed_price_per_unit_stock'])) {
            $product['computed_price_per_package_stock'] = $product['computed_price_per_unit_stock'] * $product['computed_coverage_per_package'];
        }
        if (isset($product['computed_price_per_unit_backorder'])) {
            $product['computed_price_per_package_backorder'] = $product['computed_price_per_unit_backorder'] * $product['computed_coverage_per_package'];
        }
    }

    $stockPrice = $product['computed_price_per_unit_stock'] ?? $product['computed_price_per_unit'] ?? $product['price_per_unit'] ?? $product['price_sqft'] ?? null;
    $backorderPrice = $product['computed_price_per_unit_backorder'] ?? null;
    $product['pricing'] = [
        'finalPriceStockPerUnit' => $stockPrice !== null ? (float) $stockPrice : null,
        'finalPriceBackorderPerUnit' => $backorderPrice !== null ? (float) $backorderPrice : null,
    ];

    $inventory = $product['inventory'] ?? [];
    $stockAvailable = isset($inventory['stockAvailable']) ? parse_store_numeric($inventory['stockAvailable']) : null;
    $stockAvailable = $stockAvailable !== null ? max(0, (float) $stockAvailable) : null;
    $hasInventory = $stockAvailable !== null && $stockAvailable > 0;
    $availabilityMode = $hasInventory ? 'stock' : 'backorder';
    $activePriceType = $availabilityMode === 'stock' && $stockPrice !== null ? 'stock' : ($backorderPrice !== null ? 'backorder' : $availabilityMode);

    $product['pricing']['activePriceType'] = $activePriceType;
    $product['pricing']['activePricePerUnit'] = $activePriceType === 'stock' ? $stockPrice : $backorderPrice;
    $product['pricing']['activePricePerPackage'] = $activePriceType === 'stock'
        ? ($product['computed_price_per_package_stock'] ?? null)
        : ($product['computed_price_per_package_backorder'] ?? null);
    $product['availability'] = [
        'mode' => $availabilityMode,
        'stockAvailable' => $stockAvailable,
        'allowBackorder' => $inventory['allowBackorder'] ?? true,
        'backorderLeadTimeDays' => $inventory['backorderLeadTimeDays'] ?? null,
        'notes' => $inventory['notes'] ?? [],
        'maxPurchaseQuantity' => $hasInventory ? $stockAvailable : null,
        'activePriceType' => $activePriceType,
    ];

    $installDefaults = load_store_config()['install'] ?? [];
    $product['services'] = [
        'installRate' => $type === 'molding'
            ? ($inventory['install']['moldingRate'] ?? $installDefaults['defaultMoldingRate'] ?? null)
            : ($inventory['install']['flooringRate'] ?? $installDefaults['defaultFlooringRate'] ?? null),
    ];

    $deliveryDefaults = load_store_config()['delivery']['zones'] ?? [];
    $product['delivery'] = [
        'zones' => $inventory['deliveryZones'] ?? $deliveryDefaults,
    ];

    return $product;
}

function load_store_products(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    $floorings = json_decode(@file_get_contents(__DIR__ . '/../floorings.json'), true) ?: [];
    $moldings = json_decode(@file_get_contents(__DIR__ . '/../moldings.json'), true) ?: [];
    $all = array_merge($floorings, $moldings);

    foreach ($all as &$product) {
        if (is_array($product)) {
            $product = enrich_store_product($product);
        }
    }
    unset($product);

    $cache = $all;
    return $cache;
}

function normalize_store_product(array $product): array
{
    $config = load_store_config();
    $measurementUnit = strtolower($product['measurement_unit'] ?? ($product['product_type'] === 'molding' ? 'lf' : 'sqft'));
    $packageCoverage = $product['computed_coverage_per_package'] ?? $product['coverage_per_box'] ?? $product['sqft_per_box'] ?? null;
    $packageCoverage = parse_store_numeric($packageCoverage);
    $packageCoverage = $packageCoverage !== null && $packageCoverage > 0 ? $packageCoverage : null;

    $stockPrice = $product['pricing']['finalPriceStockPerUnit'] ?? null;
    $backorderPrice = $product['pricing']['finalPriceBackorderPerUnit'] ?? null;
    $activePriceType = $product['pricing']['activePriceType'] ?? ($product['availability']['activePriceType'] ?? ($product['availability']['mode'] ?? 'stock'));
    $activePricePerUnit = $product['pricing']['activePricePerUnit'] ?? null;
    $activePricePerPackage = $product['pricing']['activePricePerPackage'] ?? null;
    $stockAvailable = $product['availability']['stockAvailable'] ?? null;
    $maxPurchaseQuantity = $product['availability']['maxPurchaseQuantity'] ?? null;

    return [
        'sku' => $product['sku'] ?? '',
        'name' => $product['name'] ?? '',
        'description' => $product['description'] ?? ($product['collection'] ?? ''),
        'productType' => $product['product_type'] ?? 'flooring',
        'category' => $product['category'] ?? null,
        'collection' => $product['collection'] ?? null,
        'brand' => $product['brand'] ?? null,
        'colorFamily' => $product['colorFamily'] ?? null,
        'tone' => $product['tone'] ?? null,
        'thickness' => $product['thickness_mm'] ?? null,
        'wearLayer' => $product['wear_layer_mil'] ?? null,
        'widthIn' => $product['width_in'] ?? null,
        'lengthIn' => $product['length_in'] ?? null,
        'measurementUnit' => $measurementUnit,
        'packageLabel' => $product['package_label'] ?? ($product['product_type'] === 'molding' ? 'package' : 'box'),
        'packageLabelPlural' => $product['package_label_plural'] ?? ($product['product_type'] === 'molding' ? 'packages' : 'boxes'),
        'packageCoverage' => $packageCoverage,
        'core' => $product['core'] ?? null,
        'pad' => $product['pad'] ?? null,
        'padMaterial' => $product['pad_material'] ?? null,
        'images' => array_values(array_filter([$product['image'] ?? null, $product['hoverImage'] ?? null])),
        'pricing' => [
            'finalPriceStockPerUnit' => $stockPrice !== null ? (float) $stockPrice : null,
            'finalPriceBackorderPerUnit' => $backorderPrice !== null ? (float) $backorderPrice : null,
            'activePriceType' => $activePriceType,
            'activePricePerUnit' => $activePricePerUnit !== null ? (float) $activePricePerUnit : null,
            'activePricePerPackage' => $activePricePerPackage !== null ? (float) $activePricePerPackage : null,
            'pricePerPackageStock' => isset($product['computed_price_per_package_stock']) ? (float) $product['computed_price_per_package_stock'] : (isset($packageCoverage, $stockPrice) ? (float) $stockPrice * $packageCoverage : null),
            'pricePerPackageBackorder' => isset($product['computed_price_per_package_backorder']) ? (float) $product['computed_price_per_package_backorder'] : (isset($packageCoverage, $backorderPrice) ? (float) $backorderPrice * $packageCoverage : null),
        ],
        'availability' => array_merge($product['availability'] ?? [], [
            'stockAvailable' => $stockAvailable !== null ? (float) $stockAvailable : null,
            'maxPurchaseQuantity' => $maxPurchaseQuantity !== null ? (float) $maxPurchaseQuantity : null,
            'mode' => $activePriceType === 'backorder' ? 'backorder' : ($product['availability']['mode'] ?? 'stock'),
            'activePriceType' => $activePriceType,
        ]),
        'services' => $product['services'] ?? [],
        'delivery' => $product['delivery'] ?? ['zones' => $config['delivery']['zones'] ?? []],
        'badges' => $product['badges'] ?? [],
        'notes' => $product['notes'] ?? [],
    ];
}
