<?php declare(strict_types=1);

namespace Bxmax\Shop\Infrastructure\Controller;

use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\Controller;
use Bxmax\Shop\Public\Service\SectionListService;

final class Section extends Controller
{
    protected function getDefaultPreFilters(): array
    {
        return [];
    }

    public function configureActions(): array
    {
        return [
            'list' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([ActionFilter\HttpMethod::METHOD_GET]),
                    new ActionFilter\CloseSession(),
                ],
            ],
        ];
    }

    public function listAction(SectionListService $sectionListService, int $page = 1, int $pageSize = 20): array
    {
        return $sectionListService->getPage($page, $pageSize)->getData();
    }
}
