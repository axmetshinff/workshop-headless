<?php

namespace Bxmax\Shop\Infrastructure\Controller;

use Bitrix\Main\Engine\Controller;

final class Health extends Controller
{
	protected function init()
	{
		parent::init();

		// initialize services and/or load modules
	}
	public function getAutoWiredParameters(): array
	{
		return [];
	}

	public function configureActions(): array
	{
		return [
			'check' => [],
		];
	}

	// replace aliases with alias form settings

	/**
	 * @ajaxAction bxmax.shop.alias.Health.check
	 */
	public function checkAction(): ?array
	{
		return [
			'ok' => true,
			'module' => 'bxmax.shop',
		];
	}
}