<?php declare(strict_types=1);

namespace Bxmax\Shop\Infrastructure\Controller;

use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\JsonPayload;
use Bitrix\Main\Validation\Engine\AutoWire\ValidationParameter;
use Bxmax\Shop\Infrastructure\Controller\Request\BasketItemAddRequest;
use Bxmax\Shop\Infrastructure\Controller\Request\BasketItemUpdateRequest;
use Bxmax\Shop\Public\Service\BasketService;

final class Basket extends ApiController
{
    public function getAutoWiredParameters(): array
    {
        return [
            new ValidationParameter(
                BasketItemAddRequest::class,
                fn () => BasketItemAddRequest::fromPayload((new JsonPayload())->getData()),
            ),
            new ValidationParameter(
                BasketItemUpdateRequest::class,
                fn () => BasketItemUpdateRequest::fromPayload((new JsonPayload())->getData()),
            ),
        ];
    }

    public function configureActions(): array
    {
        return [
            'get' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([ActionFilter\HttpMethod::METHOD_GET]),
                ],
            ],
            'add' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([ActionFilter\HttpMethod::METHOD_POST]),
                    new ActionFilter\ContentType([ActionFilter\ContentType::JSON]),
                    new ActionFilter\Csrf(),
                ],
            ],
            'update' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([ActionFilter\HttpMethod::METHOD_PATCH]),
                    new ActionFilter\ContentType([ActionFilter\ContentType::JSON]),
                    new ActionFilter\Csrf(),
                ],
            ],
            'delete' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([ActionFilter\HttpMethod::METHOD_DELETE]),
                    new ActionFilter\Csrf(),
                ],
            ],
        ];
    }

    public function getAction(BasketService $basketService): array
    {
        $basket = $basketService->getCurrent();
        $basket['csrfToken'] = bitrix_sessid();

        return $basket;
    }

    public function addAction(BasketItemAddRequest $request, BasketService $basketService): ?array
    {
        return $this->respond($basketService->add($request->productId, $request->quantity));
    }

    public function updateAction(
        int $id,
        BasketItemUpdateRequest $request,
        BasketService $basketService,
    ): ?array {
        return $this->respond($basketService->update($id, $request->quantity));
    }

    public function deleteAction(int $id, BasketService $basketService): ?array
    {
        return $this->respond($basketService->delete($id));
    }
}
