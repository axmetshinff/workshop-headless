<?php declare(strict_types=1);

namespace Bxmax\Shop\Infrastructure\Controller;

use Bitrix\Main\Engine\ActionFilter;
use Bxmax\Shop\Public\Query\IncludeQuery;
use Bxmax\Shop\Public\Service\ProductListService;
use Bxmax\Shop\Public\Service\ProductViewService;

final class Product extends ApiController
{
    public function configureActions(): array
    {
        return [
            'list' => ['prefilters' => $this->getPublicReadFilters()],
            'get' => ['prefilters' => $this->getPublicReadFilters()],
        ];
    }

    public function listAction(ProductListService $productListService, int $page = 1, int $pageSize = 20): ?array
    {
        $include = IncludeQuery::parse($this->getRequest()->getQuery('include'), ['offers', 'images']);
        if (!$include->isSuccess()) {
            return $this->respond($include);
        }

        return $this->respond($productListService->getPage($page, $pageSize, $include->getData()));
    }

    public function getAction(ProductViewService $productViewService, int $id): ?array
    {
        $include = IncludeQuery::parse($this->getRequest()->getQuery('include'), ['offers', 'images']);
        if (!$include->isSuccess()) {
            return $this->respond($include);
        }

        return $this->respond($productViewService->getById($id, $include->getData()));
    }

    /**
     * Чтение для всех: только GET, без авторизации, сессия закрывается до действия.
     */
    private function getPublicReadFilters(): array
    {
        return [
            new ActionFilter\HttpMethod([ActionFilter\HttpMethod::METHOD_GET]),
            new ActionFilter\CloseSession(),
        ];
    }
}
