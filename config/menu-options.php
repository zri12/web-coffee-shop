<?php

return [
    'groups' => [
        'temperature' => [
            'name' => 'Temperature',
            'type' => 'single',
            'is_required' => true,
            'values' => [
                ['key' => 'hot', 'name' => 'Hot', 'price_adjustment' => 0],
                ['key' => 'iced', 'name' => 'Iced', 'price_adjustment' => 0],
            ],
        ],
        'ice_level' => [
            'name' => 'Ice Level',
            'type' => 'single',
            'is_required' => false,
            'values' => [
                ['key' => 'normal', 'name' => 'Normal', 'price_adjustment' => 0],
                ['key' => 'less', 'name' => 'Less Ice', 'price_adjustment' => 0],
                ['key' => 'no_ice', 'name' => 'No Ice', 'price_adjustment' => 0],
            ],
        ],
        'sugar_level' => [
            'name' => 'Sugar Level',
            'type' => 'single',
            'is_required' => false,
            'values' => [
                ['key' => 'normal', 'name' => 'Normal', 'price_adjustment' => 0],
                ['key' => 'less', 'name' => 'Less Sugar', 'price_adjustment' => 0],
                ['key' => 'no_sugar', 'name' => 'No Sugar', 'price_adjustment' => 0],
            ],
        ],
        'size' => [
            'name' => 'Size',
            'type' => 'single',
            'is_required' => false,
            'values' => [
                ['key' => 'regular', 'name' => 'Regular', 'price_adjustment' => 0],
                ['key' => 'large', 'name' => 'Large', 'price_adjustment' => 8000],
            ],
        ],
        'portion' => [
            'name' => 'Portion',
            'type' => 'single',
            'is_required' => false,
            'values' => [
                ['key' => 'regular', 'name' => 'Regular', 'price_adjustment' => 0],
                ['key' => 'large', 'name' => 'Large', 'price_adjustment' => 5000],
            ],
        ],
        'spice_level' => [
            'name' => 'Spice Level',
            'type' => 'single',
            'is_required' => false,
            'values' => [
                ['key' => 'mild', 'name' => 'Mild', 'price_adjustment' => 0],
                ['key' => 'medium', 'name' => 'Medium', 'price_adjustment' => 0],
                ['key' => 'spicy', 'name' => 'Spicy', 'price_adjustment' => 0],
            ],
        ],
        'toppings' => [
            'name' => 'Toppings',
            'type' => 'multiple',
            'is_required' => false,
            'values' => [
                ['key' => 'chocolate', 'name' => 'Chocolate Sauce', 'price_adjustment' => 3000],
                ['key' => 'caramel', 'name' => 'Caramel Drizzle', 'price_adjustment' => 3000],
                ['key' => 'whipped', 'name' => 'Whipped Cream', 'price_adjustment' => 5000],
                ['key' => 'ice_cream', 'name' => 'Ice Cream', 'price_adjustment' => 8000],
            ],
        ],
        'sauces' => [
            'name' => 'Sauces',
            'type' => 'multiple',
            'is_required' => false,
            'values' => [
                ['key' => 'ketchup', 'name' => 'Ketchup', 'price_adjustment' => 0],
                ['key' => 'mayonnaise', 'name' => 'Mayonnaise', 'price_adjustment' => 0],
                ['key' => 'chili', 'name' => 'Chili Sauce', 'price_adjustment' => 0],
                ['key' => 'bbq', 'name' => 'BBQ Sauce', 'price_adjustment' => 2000],
            ],
        ],
    ],
    'defaults' => [
        'temperature' => true,
        'ice_level' => true,
        'sugar_level' => true,
        'size' => true,
        'portion' => true,
        'spice_level' => true,
        'toppings' => true,
        'sauces' => true,
    ],
];
