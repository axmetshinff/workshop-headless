<?php declare(strict_types=1);

namespace Bxmax\Shop\Internals\Contract\Repository;

interface SectionRepositoryInterface
{
    /**
     * @return list<array{id: int, name: string, code: string}>
     */
    public function findActive(int $limit, int $offset): array;

    public function countActive(): int;
}
