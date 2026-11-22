<?php declare(strict_types=1);

namespace Bxmax\Shop\Public\Service;

use Bitrix\Main\Result;
use Bxmax\Shop\Internals\Contract\Repository\SectionRepositoryInterface;

final class SectionListService
{
    private const MAX_PAGE_SIZE = 50;

    public function __construct(
        private readonly SectionRepositoryInterface $sectionRepository,
    ) {}

    public function getPage(int $page, int $pageSize): Result
    {
        $page = max(1, $page);
        $pageSize = min(self::MAX_PAGE_SIZE, max(1, $pageSize));
        $offset = ($page - 1) * $pageSize;

        return (new Result())->setData([
            'items' => $this->sectionRepository->findActive($pageSize, $offset),
            'meta' => [
                'page' => $page,
                'pageSize' => $pageSize,
                'total' => $this->sectionRepository->countActive(),
            ],
        ]);
    }
}
