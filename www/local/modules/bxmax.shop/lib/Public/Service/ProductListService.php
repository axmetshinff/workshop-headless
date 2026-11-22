<?php declare(strict_types=1);

namespace Bxmax\Shop\Public\Service;

use Bitrix\Main\Result;
use Bitrix\Main\UI\PageNavigation;
use Bxmax\Shop\Internals\Contract\Repository\ProductRepositoryInterface;

final class ProductListService
{
    private const MAX_PAGE_SIZE = 50;

    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    public function getPage(int $page, int $pageSize, array $include = [], array $orderBy = []): Result
    {
        $nav = (new PageNavigation('products'))
            ->setPageSize(min(self::MAX_PAGE_SIZE, max(1, $pageSize)))
            ->setCurrentPage(max(1, $page))
            ->setRecordCount($this->productRepository->countActive());

        $items = [];
        if ($nav->getCurrentPage() <= $nav->getPageCount()) {
            $items = $this->productRepository->findActive($nav->getLimit(), $nav->getOffset(), $include, $orderBy);
        }

        return (new Result())->setData([
            'items' => $items,
            'meta' => [
                'page' => $nav->getCurrentPage(),
                'pageSize' => $nav->getPageSize(),
                'total' => $nav->getRecordCount(),
            ],
        ]);
    }
}
