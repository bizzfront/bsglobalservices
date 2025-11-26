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

function load_store_orders(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    $ordersPath = __DIR__ . '/../orders.json';
    $cache = json_decode(@file_get_contents($ordersPath), true) ?: ['orders' => []];
    if (!isset($cache['orders']) || !is_array($cache['orders'])) {
        $cache['orders'] = [];
    }

    return $cache;
}

function get_inventory_reservations(string $sku): array
{
    $ordersData = load_store_orders();
    $reservations = [];
    foreach ($ordersData['orders'] as $order) {
        if (!is_array($order) || ($order['sku'] ?? null) !== $sku) {
            continue;
        }
        $status = strtolower((string)($order['status'] ?? 'active'));
        if (in_array($status, ['cancelled', 'canceled', 'fulfilled', 'closed', 'complete'], true)) {
            continue;
        }
        $inventoryId = (string)($order['inventoryId'] ?? '');
        if ($inventoryId === '') {
            continue;
        }
        $qty = parse_store_numeric($order['quantity'] ?? null);
        if ($qty === null || $qty <= 0) {
            continue;
        }
        $reservations[$inventoryId] = ($reservations[$inventoryId] ?? 0) + (float) $qty;
    }

    return $reservations;
}

function normalize_inventory_entries(array $inventory, array $defaults, string $sku): array
{
    $entries = [];
    $rawList = [];
    if (isset($inventory['inventories']) && is_array($inventory['inventories'])) {
        $rawList = $inventory['inventories'];
    }

    if (!$rawList && (isset($inventory['stockAvailable']) || isset($defaults['stockAvailable']))) {
        $rawList[] = [
            'id' => $inventory['inventoryId'] ?? ($inventory['acquiredAt'] ?? $sku . '-inv'),
            'stockAvailable' => $inventory['stockAvailable'] ?? $defaults['stockAvailable'] ?? null,
            'price_per_unit' => $inventory['price_per_unit'] ?? null,
            'price_per_package' => $inventory['price_per_package'] ?? null,
            'acquiredAt' => $inventory['acquiredAt'] ?? null,
        ];
    }

    foreach ($rawList as $index => $entry) {
        if (!is_array($entry)) {
            continue;
        }
        $id = (string)($entry['id'] ?? '');
        if ($id === '') {
            $id = $entry['acquiredAt'] ?? ($sku . '-' . $index);
        }
        $stockAvailable = parse_store_numeric($entry['stockAvailable'] ?? null);
        $pricePerUnit = parse_store_numeric($entry['price_per_unit'] ?? $entry['pricePerUnit'] ?? null);
        $pricePerPackage = parse_store_numeric($entry['price_per_package'] ?? $entry['pricePerPackage'] ?? null);
        $discount = parse_store_numeric($entry['descuento'] ?? $entry['discount'] ?? null);
        $entries[] = [
            'id' => $id,
            'acquiredAt' => $entry['acquiredAt'] ?? null,
            'stockAvailable' => $stockAvailable !== null ? max(0, (float) $stockAvailable) : null,
            'pricePerUnit' => $pricePerUnit !== null ? (float) $pricePerUnit : null,
            'pricePerPackage' => $pricePerPackage !== null ? (float) $pricePerPackage : null,
            'discount' => $discount !== null ? (float) $discount : null,
        ];
    }

    usort($entries, function ($a, $b) {
        $aDate = $a['acquiredAt'] ?? null;
        $bDate = $b['acquiredAt'] ?? null;
        if ($aDate && $bDate && $aDate !== $bDate) {
            return strcmp($aDate, $bDate);
        }
        return 0;
    });

    return $entries;
}

function merge_inventory(array $product): array
{
    $inventoryIndex = load_store_inventory_index();
    $defaults = $inventoryIndex['__defaults'] ?? [];
    $sku = $product['sku'] ?? null;
    $productInventory = $sku && isset($inventoryIndex[$sku]) ? $inventoryIndex[$sku] : [];
    $inventory = array_merge($defaults, $productInventory);
    $entries = normalize_inventory_entries($productInventory, $defaults, (string) $sku);
    $reservations = $sku ? get_inventory_reservations((string) $sku) : [];
    $activeInventory = null;
    foreach ($entries as &$entry) {
        $reserved = $reservations[$entry['id']] ?? 0;
        $available = ($entry['stockAvailable'] ?? 0) - $reserved;
        $entry['reserved'] = $reserved > 0 ? (float) $reserved : 0;
        $entry['availableAfterOrders'] = $available > 0 ? (float) $available : 0.0;
        if ($activeInventory === null && $available > 0) {
            $activeInventory = $entry;
        }
    }
    unset($entry);

    $inventory['inventories'] = $entries;
    $inventory['activeInventoryId'] = $activeInventory['id'] ?? null;
    $inventory['stockAvailable'] = $activeInventory['availableAfterOrders'] ?? 0;
    $inventory['activeInventory'] = $activeInventory;
    $inventory['reservations'] = $reservations;

    $product['inventory'] = $inventory;

    return $product;
}

function enrich_store_product(array $product): array
{
    $type = $product['product_type'] ?? 'flooring';
    $product = merge_inventory($product);
    $activeInventory = $product['inventory']['activeInventory'] ?? null;
    $inventoryPricePerUnit = $activeInventory['pricePerUnit'] ?? null;
    $inventoryPricePerPackage = $activeInventory['pricePerPackage'] ?? null;

    if (!isset($product['package_label']) || !isset($product['package_label_plural'])) {
        if ($type === 'molding') {
            $product['package_label'] = 'piece';
            $product['package_label_plural'] = 'pieces';
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
        $gainPercent = parse_store_numeric($product['ganancia'] ?? null);
        $discountPercentProduct = parse_store_numeric($product['descuento'] ?? null);
        $discountPercentInventory = parse_store_numeric($product['inventory']['descuento'] ?? $product['inventory']['discount'] ?? ($activeInventory['discount'] ?? $activeInventory['descuento'] ?? null));
        $providerPrice = parse_store_numeric($product['provider_price'] ?? $product['providerPrice'] ?? null);
        $truckloadPallets = parse_store_numeric($product['truck_load_1216_pallets'] ?? $product['truckLoad1216Pallets'] ?? null);

        $discountPercent = $discountPercentInventory !== null ? $discountPercentInventory : $discountPercentProduct;
        $gainFactor = $gainPercent !== null ? (1 + ($gainPercent / 100)) : 1.0;
        $discountValue = $discountPercent !== null ? max(0.0, min(100.0, (float) $discountPercent)) : null;
        $discountFactor = $discountValue !== null ? (1 - ($discountValue / 100)) : 1.0;

        $flooringTruckloadDefault = 0.0;
        $storeConfig = load_store_config();
        $flooringTruckload = $storeConfig['flooring']['truckload'] ?? [];
        if (!empty($flooringTruckload['tiers']) && is_array($flooringTruckload['tiers'])) {
            foreach ($flooringTruckload['tiers'] as $tier) {
                if (!empty($tier['default'])) {
                    $defaultPrice = parse_store_numeric($tier['pricePerPiece'] ?? null);
                    if ($defaultPrice !== null) {
                        $flooringTruckloadDefault = (float) $defaultPrice;
                        break;
                    }
                }
            }
        }
        if ($flooringTruckloadDefault === 0.0) {
            $defaultFromConfig = parse_store_numeric($flooringTruckload['defaultPricePerPiece'] ?? null);
            if ($defaultFromConfig !== null) {
                $flooringTruckloadDefault = (float) $defaultFromConfig;
            }
        }

        $applyAdjustments = static function (?float $base) use ($gainFactor, $discountFactor): ?float {
            if ($base === null) {
                return null;
            }

            $price = $base * $gainFactor * $discountFactor;

            return round($price, 4);
        };

        $stockBasePrice = $inventoryPricePerUnit !== null ? (float) $inventoryPricePerUnit : null;
        $backorderBasePrice = $providerPrice !== null
            ? ($providerPrice + $flooringTruckloadDefault)
            : parse_store_numeric($product['precio_base'] ?? null);

        $computedStockPrice = $applyAdjustments($stockBasePrice);
        if ($computedStockPrice !== null) {
            $product['computed_price_per_unit_stock'] = $computedStockPrice;
            $product['computed_price_per_unit'] = $computedStockPrice;
            $product['price_per_unit'] = $computedStockPrice;
        }

        $computedBackorderPrice = $applyAdjustments($backorderBasePrice);
        if ($computedBackorderPrice !== null) {
            $product['computed_price_per_unit_backorder'] = $computedBackorderPrice;
        }

        if (!isset($product['computed_price_per_unit']) && $computedBackorderPrice !== null) {
            $product['computed_price_per_unit'] = $computedBackorderPrice;
            $product['price_per_unit'] = $computedBackorderPrice;
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

    if ($inventoryPricePerPackage !== null) {
        $product['computed_price_per_package_stock'] = (float) $inventoryPricePerPackage;
    } elseif ($inventoryPricePerUnit !== null && isset($product['computed_coverage_per_package']) && isset($product['computed_price_per_unit_stock'])) {
        $product['computed_price_per_package_stock'] = (float) $product['computed_price_per_unit_stock'] * (float) $product['computed_coverage_per_package'];
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
    $allowBackorder = $inventory['allowBackorder'] ?? true;
    $maxPurchaseQuantity = $hasInventory && !$allowBackorder ? $stockAvailable : null;

    $product['pricing']['activePriceType'] = $activePriceType;
    $product['pricing']['activePricePerUnit'] = $activePriceType === 'stock' ? $stockPrice : $backorderPrice;
    $product['pricing']['activePricePerPackage'] = $activePriceType === 'stock'
        ? ($product['computed_price_per_package_stock'] ?? null)
        : ($product['computed_price_per_package_backorder'] ?? null);
    $product['availability'] = [
        'mode' => $availabilityMode,
        'stockAvailable' => $stockAvailable,
        'allowBackorder' => $allowBackorder,
        'backorderLeadTimeDays' => $inventory['backorderLeadTimeDays'] ?? null,
        'notes' => $inventory['notes'] ?? [],
        'maxPurchaseQuantity' => $maxPurchaseQuantity,
        'activePriceType' => $activePriceType,
        'activeInventoryId' => $inventory['activeInventoryId'] ?? null,
        'inventoryEntries' => $inventory['inventories'] ?? [],
        'reservations' => $inventory['reservations'] ?? [],
        'activeInventoryStock' => $inventory['activeInventory']['availableAfterOrders'] ?? $stockAvailable,
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
    $taxesOmit = !empty($product['taxes_omit']);
    $providerPrice = parse_store_numeric($product['provider_price'] ?? $product['providerPrice'] ?? null);
    $truckLoadPallets = parse_store_numeric($product['truck_load_1216_pallets'] ?? $product['truckLoad1216Pallets'] ?? null);
    $gainPercent = parse_store_numeric($product['ganancia'] ?? null);
    $discountPercent = parse_store_numeric($product['descuento'] ?? null);
    if (($product['product_type'] ?? '') === 'molding') {
        $lengthFt = parse_store_numeric($product['length_ft'] ?? null);
        if ($lengthFt !== null && $lengthFt > 0) {
            $packageCoverage = $lengthFt;
        }
    }
    $packageCoverage = $packageCoverage !== null && $packageCoverage > 0 ? $packageCoverage : null;

    $stockPrice = $product['pricing']['finalPriceStockPerUnit'] ?? null;
    $backorderPrice = $product['pricing']['finalPriceBackorderPerUnit'] ?? null;
    $activePriceType = $product['pricing']['activePriceType'] ?? ($product['availability']['activePriceType'] ?? ($product['availability']['mode'] ?? 'stock'));
    $activePricePerUnit = $product['pricing']['activePricePerUnit'] ?? null;
    $activePricePerPackage = $product['pricing']['activePricePerPackage'] ?? null;
    $stockAvailable = $product['availability']['stockAvailable'] ?? null;
    $maxPurchaseQuantity = $product['availability']['maxPurchaseQuantity'] ?? null;
    $piecesPerBox = parse_store_numeric($product['pieces_per_box'] ?? null);

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
        'packageLabel' => $product['package_label'] ?? ($product['product_type'] === 'molding' ? 'piece' : 'box'),
        'packageLabelPlural' => $product['package_label_plural'] ?? ($product['product_type'] === 'molding' ? 'pieces' : 'boxes'),
        'packageCoverage' => $packageCoverage,
        'piecesPerBox' => $piecesPerBox !== null && $piecesPerBox > 0 ? $piecesPerBox : null,
        'core' => $product['core'] ?? null,
        'pad' => $product['pad'] ?? null,
        'padMaterial' => $product['pad_material'] ?? null,
        'images' => array_values(array_filter([$product['image'] ?? null, $product['hoverImage'] ?? null])),
        'taxesOmit' => $taxesOmit,
        'pricing' => [
            'finalPriceStockPerUnit' => $stockPrice !== null ? (float) $stockPrice : null,
            'finalPriceBackorderPerUnit' => $backorderPrice !== null ? (float) $backorderPrice : null,
            'activePriceType' => $activePriceType,
            'activePricePerUnit' => $activePricePerUnit !== null ? (float) $activePricePerUnit : null,
            'activePricePerPackage' => $activePricePerPackage !== null ? (float) $activePricePerPackage : null,
            'pricePerPackageStock' => isset($product['computed_price_per_package_stock']) ? (float) $product['computed_price_per_package_stock'] : (isset($packageCoverage, $stockPrice) ? (float) $stockPrice * $packageCoverage : null),
            'pricePerPackageBackorder' => isset($product['computed_price_per_package_backorder']) ? (float) $product['computed_price_per_package_backorder'] : (isset($packageCoverage, $backorderPrice) ? (float) $backorderPrice * $packageCoverage : null),
            'providerPrice' => $providerPrice !== null ? (float) $providerPrice : null,
            'truckLoadPallets' => $truckLoadPallets !== null ? (float) $truckLoadPallets : null,
            'gainPercent' => $gainPercent !== null ? (float) $gainPercent : null,
            'discountPercent' => $discountPercent !== null ? (float) $discountPercent : null,
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
