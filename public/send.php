<?php

include_once '../autoload.php';

use tools\WooCommerce;

$data = [
    'name' => 'Ship Your Idea',
    'type' => 'variable',
    'description' => 'Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.',
    'short_description' => 'Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.',
    'categories' => [
        [
            'id' => 9
        ],
        [
            'id' => 14
        ]
    ],
    'images' => [
        [
            'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_4_front.jpg',
            'position' => 0
        ],
        [
            'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_4_back.jpg',
            'position' => 1
        ],
        [
            'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_3_front.jpg',
            'position' => 2
        ],
        [
            'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_3_back.jpg',
            'position' => 3
        ]
    ],
    'attributes' => [
        [
            'id' => 6,
            'position' => 0,
            'visible' => false,
            'variation' => true,
            'options' => [
                'Black',
                'Green'
            ]
        ],
        [
            'name' => 'Size',
            'position' => 0,
            'visible' => true,
            'variation' => true,
            'options' => [
                'S',
                'M'
            ]
        ]
    ],
    'default_attributes' => [
        [
            'id' => 6,
            'option' => 'Black'
        ],
        [
            'name' => 'Size',
            'option' => 'S'
        ]
    ],
    'variations' => [
        [
            'regular_price' => '19.99',
            'image' => [
                [
                    'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_4_front.jpg',
                    'position' => 0
                ]
            ],
            'attributes' => [
                [
                    'id' => 6,
                    'option' => 'black'
                ],
                [
                    'name' => 'Size',
                    'option' => 'S'
                ]
            ]
        ],
        [
            'regular_price' => '19.99',
            'image' => [
                [
                    'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_3_front.jpg',
                    'position' => 0
                ]
            ],
            'attributes' => [
                [
                    'id' => 6,
                    'option' => 'green'
                ],
                [
                    'name' => 'Size',
                    'option' => 'M'
                ]
            ]
        ]
    ]
];

$woocommerce = new WooCommerce();
$result = $woocommerce->createProduct($data);

var_dump($result);