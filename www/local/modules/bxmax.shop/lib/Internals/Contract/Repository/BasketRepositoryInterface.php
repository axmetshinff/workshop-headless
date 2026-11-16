<?php declare(strict_types=1);

namespace Bxmax\Shop\Internals\Contract\Repository;

use Bitrix\Main\Result;

interface BasketRepositoryInterface
{
    /**
     * @return array{
     *     items: list<array{
     *         id: int,
     *         productId: int,
     *         name: string,
     *         quantity: float,
     *         price: array{amount: string, currency: string},
     *         total: array{amount: string, currency: string},
     *     }>,
     *     total: array{amount: string, currency: string},
     * }
     */
    public function getCurrent(): array;

    public function add(int $productId, int $quantity): Result;

    public function update(int $itemId, int $quantity): Result;

    public function delete(int $itemId): Result;
}
