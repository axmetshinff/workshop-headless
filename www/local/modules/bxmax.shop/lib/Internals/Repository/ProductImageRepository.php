<?php declare(strict_types=1);

namespace Bxmax\Shop\Internals\Repository;

use Bitrix\Iblock\Elements\ElementClothesTable;
use Bitrix\Main\Context;
use Bitrix\Main\FileTable;
use Bitrix\Main\Web\Uri;
use Bxmax\Shop\Internals\Repository\Mapper\ProductMapper;

final class ProductImageRepository
{
    /** @param list<int> $productIds */
    public function findByProductIds(array $productIds): array
    {
        if ($productIds === []) {
            return [];
        }

        $rows = ElementClothesTable::getList([
            'select' => ['ID', 'DETAIL_PICTURE', 'PREVIEW_PICTURE', 'PHOTO' => 'MORE_PHOTO.VALUE'],
            'filter' => ['@ID' => $productIds],
            'order' => ['ID' => 'ASC', 'MORE_PHOTO.ID' => 'ASC'],
        ]);

        $byProduct = [];
        $allIds = [];
        while ($row = $rows->fetch()) {
            foreach (['DETAIL_PICTURE', 'PREVIEW_PICTURE', 'PHOTO'] as $field) {
                $id = (int)$row[$field];
                if ($id > 0) {
                    $byProduct[(int)$row['ID']][$id] = $id;
                    $allIds[$id] = $id;
                }
            }
        }
        if (!$allIds) {
            return [];
        }

        $files = FileTable::getList(['filter' => ['@ID' => array_values($allIds)]]);
        $context = Context::getCurrent();
        $scheme = $context->getRequest()->isHttps() ? 'https' : 'http';
        // SERVER.HTTP_HOST содержит внешний порт, например shop.bitrix:8765.
        $origin = new Uri($scheme . '://' . $context->getServer()->getHttpHost());
        $urls = [];
        while ($file = $files->fetch()) {
            if (!str_starts_with((string)$file['CONTENT_TYPE'], 'image/')) {
                continue;
            }
            // GetFileSRC принимает уже загруженную строку и учитывает обработчик хранилища.
            $src = \CFile::GetFileSRC($file);
            if (str_starts_with($src, '//')) {
                $src = $scheme . ':' . $src;
            }
            $uri = (new Uri($src))->resolveRelativeUri($origin);
            if (in_array($uri->getScheme(), ['http', 'https'], true)) {
                $urls[(int)$file['ID']] = $uri->getUri();
            }
        }

        $images = [];
        foreach ($byProduct as $parentId => $ids) {
            $images[$parentId] = ProductMapper::imageUrls($ids, $urls);
        }
        return $images;
    }
}
