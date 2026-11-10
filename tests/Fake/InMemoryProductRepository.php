<?php declare(strict_types=1);

namespace Tests\Fake;

use Bxmax\Shop\Internals\Contract\Repository\ProductRepositoryInterface;

/**
 * Репозиторий для тестов: товары передаёт сам тест.
 */
final class InMemoryProductRepository implements ProductRepositoryInterface
{
    /**
     * @param list<array{id: int, name: string, code: string, price: ?array{amount: string, currency: string}}> $products
     */
    public function __construct(
        private readonly array $products,
    ) {}

    public function findActive(int $limit, int $offset): array
    {
        return array_slice($this->products, $offset, $limit);
    }

    public function countActive(): int
    {
        return count($this->products);
    }
}
