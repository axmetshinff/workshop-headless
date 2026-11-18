<?php declare(strict_types=1);

namespace Bxmax\Shop\Public\Service;

use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Bxmax\Shop\Internals\Contract\Repository\ProductRepositoryInterface;
use Bxmax\Shop\Public\Enum\ErrorCode;

final class ProductViewService
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    /**
     * Карточка активного товара. Неактивный товар неотличим от несуществующего.
     */
    public function getById(int $id, array $include = []): Result
    {
        $product = $this->productRepository->findActiveById($id, $include);
        if ($product === null) {
            return (new Result())->addError(new Error('Товар не найден', ErrorCode::ProductNotFound->value));
        }

        return (new Result())->setData($product);
    }
}
