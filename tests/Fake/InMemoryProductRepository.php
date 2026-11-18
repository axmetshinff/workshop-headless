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
        private readonly array $relations = [],
    ) {}

    public function findActive(int $limit, int $offset, array $include = [], array $orderBy = []): array
    {
        $products = $this->products;
        if ($orderBy) {
            usort($products, static function (array $left, array $right) use ($orderBy): int {
                foreach ($orderBy as $name => $direction) {
                    $key = strtolower($name);
                    $comparison = $left[$key] <=> $right[$key];
                    if ($comparison !== 0) {
                        return $direction === 'DESC' ? -$comparison : $comparison;
                    }
                }
                return 0;
            });
        }

        return array_map(fn (array $product) => $this->withRelations($product, $include),
            array_slice($products, $offset, $limit));
    }

    public function countActive(): int
    {
        return count($this->products);
    }

    public function findActiveById(int $id, array $include = []): ?array
    {
        foreach ($this->products as $product) {
            if ($product['id'] === $id) {
                return $this->withRelations($product, $include) + ['categoryId' => null, 'article' => null, 'descriptionHtml' => ''];
            }
        }

        return null;
    }

    private function withRelations(array $product, array $include): array
    {
        foreach ($include as $name) {
            $product[$name] = $this->relations[$product['id']][$name] ?? [];
        }

        return $product;
    }
}
