<?php
function enrich_store_product(array $product): array
{
    $type = $product['product_type'] ?? 'flooring';

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
        $price = null;
        if (isset($product['computed_price_per_unit'])) {
            $price = (float) $product['computed_price_per_unit'];
        } elseif (isset($product['price_per_unit'])) {
            $price = (float) $product['price_per_unit'];
        } elseif (isset($product['price_sqft'])) {
            $price = (float) $product['price_sqft'];
        }
        if ($price !== null) {
            $product['computed_price_per_unit'] = $price;
            $product['price_per_unit'] = $price;
        }
    }

    $coverage = $product['coverage_per_box'] ?? $product['sqft_per_box'] ?? null;
    if (!is_numeric($coverage) || (float) $coverage <= 0) {
        $length = isset($product['length_ft']) ? (float) $product['length_ft'] : null;
        $pieces = isset($product['pieces_per_box']) ? (float) $product['pieces_per_box'] : null;
        if ($length && $pieces) {
            $coverage = $length * $pieces;
        } else {
            $coverage = null;
        }
    }
    if ($coverage !== null) {
        $product['computed_coverage_per_package'] = (float) $coverage;
        if (!isset($product['coverage_per_box'])) {
            $product['coverage_per_box'] = $product['computed_coverage_per_package'];
        }
    }

    if (isset($product['computed_price_per_unit']) && isset($product['computed_coverage_per_package'])) {
        $product['computed_price_per_package'] = $product['computed_price_per_unit'] * $product['computed_coverage_per_package'];
    }

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
