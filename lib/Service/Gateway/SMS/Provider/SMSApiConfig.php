<?php

declare(strict_types=1);

/**
 * @author Marcin Kot <kodek11@gmail.com>
 *
 * Nextcloud - Two-factor Gateway for SMSApi.com
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\TwoFactorGateway\Service\Gateway\SMS\Provider;

use function array_intersect;
use OCA\TwoFactorGateway\AppInfo\Application;
use OCA\TwoFactorGateway\Exception\ConfigurationException;
use OCP\IConfig;

class SMSapiConfig implements IProviderConfig {
	private const expected = [
		'smsapi_token',
		'smsapi_sender',
	];

	/** @var IConfig */
	private $config;

	public function __construct(IConfig $config) {
		$this->config = $config;
	}

	private function getOrFail(string $key): string {
		$val = $this->config->getAppValue(Application::APP_ID, $key, null);
		if (is_null($val)) {
			throw new ConfigurationException();
		}
		return $val;
	}

	public function getToken(): string {
		return $this->getOrFail('smsapi_token');
	}

	public function getSender(): string {
		return $this->getOrFail('smsapi_sender');
	}

	public function setToken(string $token) {
		$this->config->setAppValue(Application::APP_ID, 'smsapi_token', $token);
	}

	public function setSender(string $sender) {
		$this->config->setAppValue(Application::APP_ID, 'smsapi_sender', $sender);
	}

	public function isComplete(): bool {
		$set = $this->config->getAppKeys(Application::APP_ID);
		return count(array_intersect($set, self::expected)) === count(self::expected);
	}

	public function remove() {
		foreach (self::expected as $key) {
			$this->config->deleteAppValue(Application::APP_ID, $key);
		}
	}
}
