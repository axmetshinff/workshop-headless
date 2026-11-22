<?php declare(strict_types=1);

namespace Bxmax\Shop\Internals\Repository;

use Bxmax\Shop\Internals\Contract\Repository\SectionRepositoryInterface;

final class IblockSectionRepository implements SectionRepositoryInterface
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
