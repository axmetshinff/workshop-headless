<?php declare(strict_types=1);

namespace Bxmax\Shop\Internals\Repository;

use Bxmax\Shop\Internals\Contract\Repository\ProductRepositoryInterface;

final class IblockProductRepository implements ProductRepositoryInterface
{
    public function findActive(int $limit, int $offset): array
    {
        return [];
    }

    public function countActive(): int
    {
        return 0;
    }
}
