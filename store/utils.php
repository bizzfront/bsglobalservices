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
        $components = [
            'truck_load_1216_pallets',
            'precio_de_carga',
            'storage_por_sqft_depende_del_mes',
            'transporte_mano_de_obra_por_sqft',
            'margen_final',
        ];
        $price = 0.0;
        $hasComponent = false;
        foreach ($components as $key) {
            if (!array_key_exists($key, $product)) {
                continue;
            }
            $value = parse_store_numeric($product[$key]);
            if ($value === null) {
                continue;
            }
            $price += $value;
            $hasComponent = true;
        }

        if ($hasComponent) {
            $product['computed_price_per_unit'] = round($price, 4);
            $product['price_per_unit'] = $product['computed_price_per_unit'];
        } else {
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
