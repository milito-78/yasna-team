<?php

namespace App\Infrastructure\Payments\Drivers;

use App\Infrastructure\Payments\Contracts\PaymentMethod;
use App\Infrastructure\Payments\Exceptions\InvalidConfigException;

abstract class GatewayAbstract implements PaymentMethod
{
    /**
     *
     * @param array $config
     * @throws InvalidConfigException
     */
    public function __construct(
        protected readonly array $config
    )
    {
        $this->checkConfig();
    }

    /**
     * Check config not empty
     *
     * @throws InvalidConfigException
     */
    private function checkConfig(): void
    {
        if (empty($this->config)){
            throw new InvalidConfigException();
        }
    }

    protected function buildUrl(array $data):string{
        return $this->config["url"] . "?" . http_build_query($data);
    }
}
