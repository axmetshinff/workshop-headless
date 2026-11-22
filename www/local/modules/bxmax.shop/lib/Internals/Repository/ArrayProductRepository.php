<?php declare(strict_types=1);

namespace Bxmax\Shop\Internals\Repository;

use Bxmax\Shop\Internals\Contract\Repository\ProductRepositoryInterface;

final class ArrayProductRepository implements ProductRepositoryInterface
{
    /**
     * @var list<array{id: int, name: string, code: string, price: array{amount: string, currency: string}}>
     */
    private const ITEMS = [
        [
            'id' => 1,
            'name' => 'Футболка',
            'code' => 't-shirt',
            'price' => ['amount' => '1990.00', 'currency' => 'RUB'],
        ],
        [
            'id' => 2,
            'name' => 'Худи',
            'code' => 'hoodie',
            'price' => ['amount' => '4990.00', 'currency' => 'RUB'],
        ],
        [
            'id' => 3,
            'name' => 'Кепка',
            'code' => 'cap',
            'price' => ['amount' => '1290.00', 'currency' => 'RUB'],
        ],
    ];

    public function findActive(int $limit, int $offset): array
    {
        return array_values(array_slice(self::ITEMS, $offset, $limit));
    }

    public function countActive(): int
    {
        return count(self::ITEMS);
    }
}
