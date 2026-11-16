<?php declare(strict_types=1);

namespace Bxmax\Shop\Public\Service;

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
}
