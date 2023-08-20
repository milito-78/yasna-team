<?php

namespace App\Infrastructure\Payments;

use App\Infrastructure\Payments\Contracts\PaymentMethod;
use App\Infrastructure\Payments\Exceptions\InvalidProviderException;
use Illuminate\Contracts\Container\BindingResolutionException;

class Factory
{

    /**
     * Hold drivers classes
     *
     * @var array|string[]
     */
    private array $drivers = [];

    /**
     * Hold each driver configs
     *
     * @var array
     */
    private array $driversConfig = [];

    /**
     * Payments config
     *
     * @var array
     */
    private array $config;

    /**
     * @param array $config
     * @throws InvalidProviderException
     */
    public function __construct(array $config = [])
    {
        $this->config = empty($config) ? config("payment"):$config;
        $this->fillDrivers();
    }

    /**
     * Create payment method class with input
     *
     * @param PaymentMethodEnum $method
     * @return PaymentMethod
     * @throws InvalidProviderException|BindingResolutionException
     */
    public function getPaymentMethod(PaymentMethodEnum $method): PaymentMethod
    {
        if (key_exists($method->value,$this->drivers)){
            return new $this->drivers[$method->value]($this->driversConfig[$method->value]);
        }

        throw new InvalidProviderException("Invalid driver.");
    }

    /**
     * @throws InvalidProviderException
     */
    private function fillDrivers():void {
        if (key_exists("drivers",$this->config) && !empty($this->config["drivers"])){
            foreach ($this->config["drivers"] as $key => $driver) {
                $this->drivers[$key] = $driver["class"];
                unset($driver["class"]);
                $this->driversConfig[$key] = $driver;
            }
            return;
        }

        throw new InvalidProviderException("Config drivers list is not exists.");
    }
}
