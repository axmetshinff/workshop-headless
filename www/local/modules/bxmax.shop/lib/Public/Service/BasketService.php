<?php declare(strict_types=1);

namespace Bxmax\Shop\Public\Service;

use Bitrix\Main\Result;
use Bxmax\Shop\Internals\Contract\Repository\BasketRepositoryInterface;

final class BasketService
{
    public function __construct(
        private readonly BasketRepositoryInterface $basketRepository,
    ) {}

    public function getCurrent(): array
    {
        return $this->basketRepository->getCurrent();
    }

    public function add(int $productId, int $quantity): Result
    {
        return $this->basketRepository->add($productId, $quantity);
    }

    public function update(int $itemId, int $quantity): Result
    {
        return $this->basketRepository->update($itemId, $quantity);
    }

    public function delete(int $itemId): Result
    {
        return $this->basketRepository->delete($itemId);
    }
}
