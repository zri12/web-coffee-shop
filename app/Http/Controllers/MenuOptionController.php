<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Support\Str;

class MenuOptionController extends Controller
{
    public function forMenu(Menu $menu)
    {
        $optionDefinitions = config('menu-options.groups', []);
        $category = $menu->category;
        $flags = $category ? $category->option_flags_with_defaults : config('menu-options.defaults', []);

        $groups = [];
        foreach ($optionDefinitions as $key => $definition) {
            $enabled = $flags[$key] ?? false;
            if (!$enabled) {
                continue;
            }

            $values = [];
            foreach ($definition['values'] ?? [] as $value) {
                $valueKey = $value['key'] ?? Str::slug($value['name'] ?? $key, '-');
                $values[] = [
                    'id' => $key . '-' . $valueKey,
                    'name' => $value['name'] ?? ucfirst(str_replace('_', ' ', $valueKey)),
                    'price_adjustment' => (float) ($value['price_adjustment'] ?? 0),
                    'stock' => null,
                    'is_available' => true,
                ];
            }

            if (empty($values)) {
                continue;
            }

            $groups[] = [
                'id' => $key,
                'name' => $definition['name'] ?? ucfirst(str_replace('_', ' ', $key)),
                'type' => $definition['type'] ?? 'single',
                'is_required' => !empty($definition['is_required']),
                'values' => $values,
            ];
        }

        $addonValues = collect($menu->addons ?? [])->map(function ($addon) {
            $name = trim($addon['name'] ?? '');
            if ($name === '') {
                return null;
            }
            $price = is_numeric($addon['price'] ?? null) ? (float) $addon['price'] : 0;
            return [
                'id' => 'addon-' . Str::slug($name, '-'),
                'name' => $name,
                'price_adjustment' => max(0, $price),
                'stock' => null,
                'is_available' => true,
            ];
        })->filter()->values()->all();

        if (!empty($addonValues)) {
            $groups[] = [
                'id' => 'add_ons',
                'name' => 'Add-Ons',
                'type' => 'multiple',
                'is_required' => false,
                'values' => $addonValues,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'menu_id' => $menu->id,
                'base_price' => (float) $menu->price,
                'option_groups' => $groups,
            ],
        ]);
    }
}
