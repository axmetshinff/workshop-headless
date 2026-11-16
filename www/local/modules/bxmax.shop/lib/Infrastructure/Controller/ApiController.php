<?php declare(strict_types=1);

namespace Bxmax\Shop\Infrastructure\Controller;

use Bitrix\Main\Context;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\AutoWire\BinderArgumentException;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\HttpResponse;
use Bitrix\Main\Response;
use Bitrix\Main\Result;
use Bxmax\Shop\Public\Enum\ErrorCode;

/**
 * Общая часть контроллеров /api/v1: без фильтров по умолчанию, ответ из Result сервиса.
 */
abstract class ApiController extends Controller
{
    protected function runProcessingBinderThrowable(BinderArgumentException $e): void
    {
        parent::runProcessingBinderThrowable($e);
        Context::getCurrent()->getResponse()->setStatus(400);
    }

    public function finalizeResponse(Response $response): void
    {
        if (!$response instanceof HttpResponse) {
            return;
        }

        $status = match (true) {
            $this->getErrorByCode(ActionFilter\Authentication::ERROR_INVALID_AUTHENTICATION) !== null => 401,
            $this->getErrorByCode(ActionFilter\Csrf::ERROR_INVALID_CSRF) !== null => 403,
            $this->getErrorByCode(ActionFilter\HttpMethod::ERROR_INVALID_HTTP_METHOD) !== null => 405,
            $this->getErrorByCode(ActionFilter\ContentType::ERROR_INVALID_CONTENT_TYPE) !== null => 415,
            default => null,
        };

        if ($status !== null) {
            $response->setStatus($status);
        }
    }

    protected function getDefaultPreFilters(): array
    {
        return [];
    }

    /**
     * Успех — данные результата. Ошибка — ошибки в конверт и HTTP-статус по коду первой ошибки.
     */
    protected function respond(Result $result): ?array
    {
        if ($result->isSuccess()) {
            return $result->getData();
        }

        $this->addErrors($result->getErrors());

        $code = ErrorCode::tryFrom((string)$result->getErrors()[0]->getCode());
        if ($code !== null) {
            Context::getCurrent()->getResponse()->setStatus($code->httpStatus());
        }

        return null;
    }
}
