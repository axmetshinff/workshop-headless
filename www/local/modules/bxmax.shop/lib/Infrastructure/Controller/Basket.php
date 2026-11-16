<?php declare(strict_types=1);

namespace Bxmax\Shop\Infrastructure\Controller;

use Bitrix\Main\Engine\ActionFilter;
use Bxmax\Shop\Public\Service\BasketService;

final class Basket extends ApiController
{
    public function configureActions(): array
    {
        return [
            'get' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([ActionFilter\HttpMethod::METHOD_GET]),
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
}
