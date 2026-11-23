<?php declare(strict_types=1);

return [
    'controllers' => [
        'value' => [
            'defaultNamespace' => '\\Bxmax\\Shop\\Infrastructure\\Controller',
        ],
        'readonly' => true,
    ],
    'services' => [
        'value' => [
            \Bxmax\Shop\Internals\Contract\Repository\ProductRepositoryInterface::class => [
                'className' => \Bxmax\Shop\Internals\Repository\IblockProductRepository::class,
            ],
            \Bxmax\Shop\Internals\Contract\Repository\SectionRepositoryInterface::class => [
                'className' => \Bxmax\Shop\Internals\Repository\IblockSectionRepository::class,
            ],
            \Bxmax\Shop\Internals\Contract\Repository\BasketRepositoryInterface::class => [
                'className' => \Bxmax\Shop\Internals\Repository\SaleBasketRepository::class,
            ],
        ],
        'readonly' => true,
    ],
];
