<?php
return [
    [
        'name' => 'vogame',
        'endpoint' => env('VOGAME_ENDPOINT', 'https://vogame.id'),
        'mbox_url' => env('VOGAME_MBOX_URL','https://vogame.id/api/v1/cart/mistery'),
        'mbox_header_auth' => env('VOGAME_HEADER_AUTH', 'AGFJB93Ufksbk9BJDSFsdhfb492hkhHKBSudh4bfslj'),
        'prefer_range' => env('VOGAME_PREFER_RANGE', 0),
        'enabled' => (int) env('VOGAME_ENABLED', 1),
        'category' => env('VOGAME_CATEGORY', 'goods'),
    ],
    [
        'name' => 'puganesia',
        'endpoint' => env('PUGANESIA_ENDPOINT', 'https://puganesia.id'),
        'mbox_url' => env('PUGANESIA_MBOX_URL','https://puganesia.id/api/v1/cart/mistery'),
        'mbox_header_auth' => env('PUGANESIA_HEADER_AUTH', 'AGFJB93Ufksbk9BJDSFsdhfb492hkhHKBSudh4bfslj'),
        'prefer_range' => env('PUGANESIA_PREFER_RANGE', 0),
        'enabled' => (int) env('PUGANESIA_ENABLED', 1),
        'category' => env('PUGANESIA_CATEGORY', 'goods'),
    ],
    [
        'name' => 'babyshop',
        'endpoint' => env('BABYSHOP_ENDPOINT', 'https://babyshop'),
        'mbox_url' => env('BABYSHOP_MBOX_URL','https://babyshop/api/v1/cart/mistery'),
        'mbox_header_auth' => env('BABYSHOP_HEADER_AUTH', 'AGFJB93Ufksbk9BJDSFsdhfb492hkhHKBSudh4bfslj'),
        'prefer_range' => env('BABYSHOP_PREFER_RANGE', 0),
        'enabled' => (int) env('BABYSHOP_ENABLED', 1),
        'category' => env('BABYSHOP_CATEGORY', 'goods'),
    ],
    [
        'name' => 'hpshop',
        'endpoint' => env('HPSHOP_ENDPOINT', 'https://hpshop'),
        'mbox_url' => env('HPSHOP_MBOX_URL','https://hpshop/api/v1/cart/mistery'),
        'mbox_header_auth' => env('HPSHOP_HEADER_AUTH', 'AGFJB93Ufksbk9BJDSFsdhfb492hkhHKBSudh4bfslj'),
        'prefer_range' => env('HPSHOP_PREFER_RANGE', 0),
        'enabled' => (int) env('HPSHOP_ENABLED', 1),
        'category' => env('HPSHOP_CATEGORY', 'goods'),
    ],
    [
        'name' => 'skincare',
        'endpoint' => env('SKINCARE_ENDPOINT', 'https://skincare'),
        'mbox_url' => env('SKINCARE_MBOX_URL','https://skincare/api/v1/cart/mistery'),
        'mbox_header_auth' => env('SKINCARE_HEADER_AUTH', 'AGFJB93Ufksbk9BJDSFsdhfb492hkhHKBSudh4bfslj'),
        'prefer_range' => env('SKINCARE_PREFER_RANGE', 0),
        'enabled' => (int) env('SKINCARE_ENABLED', 1),
        'category' => env('SKINCARE_CATEGORY', 'goods'),
    ]
];
