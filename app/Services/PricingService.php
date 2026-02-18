<?php

namespace App\Services;

use App\Models\Menu;

class PricingService
{
    /**
     * Normalize option payload coming from front-end option groups.
     */
    public function normalizeOptions(array $options): array
    {
        // Support option_groups payload
        if (isset($options['option_groups']) && is_array($options['option_groups'])) {
            $normalized = [];
            foreach ($options['option_groups'] as $group) {
                if (!isset($group['id'])) {
                    continue;
                }
                $selectedValues = $group['selected_values'] ?? [];
                $normalized[$group['id']] = array_map(function ($value) {
                    return [
                        'id' => $value['id'] ?? null,
                        'name' => $value['name'] ?? null,
                        'price_adjustment' => (float) ($value['price_adjustment'] ?? 0),
                    ];
                }, $selectedValues);
            }
            ksort($normalized);
            return $normalized;
        }

        // Legacy flat options e.g. size/portion/addOns
        return $options;
    }

    /**
     * Calculate price based on menu base price and normalized options.
     */
    public function calculate(Menu $menu, array $options, int $quantity = 1): array
    {
        $basePrice = (float) $menu->price;
        $normalized = $this->normalizeOptions($options);
        $additional = 0;

        $simpleOptions = [
            'size' => null,
            'temperature' => null,
            'sugar_level' => null,
            'ice_level' => null,
            'portion' => null,
            'spice_level' => null,
            'addons' => [],
        ];

        // Option groups with price adjustments
        foreach ($normalized as $groupId => $values) {
            if (!is_array($values)) {
                // Skip scalar/special fields like specialRequest
                continue;
            }

            // Ensure values is a list of option entries
            if (!empty($values) && !isset($values[0]) && isset($values['id'])) {
                $values = [$values];
            }
            if (!is_array($values)) {
                continue;
            }

            foreach ($values as $value) {
                $additional += (float) ($value['price_adjustment'] ?? 0);
            }

            $first = $values[0] ?? null;
            switch ($groupId) {
                case 'size':
                    $simpleOptions['size'] = $first['id'] ?? null;
                    break;
                case 'temperature':
                    $simpleOptions['temperature'] = $first['id'] ?? null;
                    break;
                case 'sugar_level':
                    $simpleOptions['sugar_level'] = $first['id'] ?? null;
                    break;
                case 'ice_level':
                    $simpleOptions['ice_level'] = $first['id'] ?? null;
                    break;
                case 'portion':
                    $simpleOptions['portion'] = $first['id'] ?? null;
                    break;
                case 'spice_level':
                    $simpleOptions['spice_level'] = $first['id'] ?? null;
                    break;
                case 'add_ons':
                case 'addons':
                    $simpleOptions['addons'] = array_map(fn ($v) => $v['id'] ?? $v['name'] ?? null, $values);
                    break;
                default:
                    break;
            }
        }

        if (isset($options['addOns']) && is_array($options['addOns'])) {
            $simpleOptions['addons'] = array_merge($simpleOptions['addons'], $options['addOns']);
        }

        $unitPrice = $basePrice + $additional;
        $subtotal = $unitPrice * max(1, $quantity);

        return [
            'base_price' => $basePrice,
            'options' => $simpleOptions,
            'raw_options' => $options,
            'normalized_options' => $normalized,
            'unit_price' => $unitPrice,
            'subtotal' => $subtotal,
        ];
    }

    public function optionSignature(array $normalizedOptions): string
    {
        ksort($normalizedOptions);
        return md5(json_encode($normalizedOptions));
    }
}
