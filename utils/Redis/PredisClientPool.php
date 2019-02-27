<?php declare(strict_types=1);

namespace Utils\Redis;

use Predis\Client;

final class PredisClientPool
{
    private $configurations;

    private $pool;

    public function __construct(array $configurations)
    {
        $this->configurations = $configurations;
        $this->pool = [];
    }

    public function client(string $name): Client
    {
        if (isset($this->pool[$name])) {
            return $this->pool[$name];
        }

        if (isset($this->configurations[$name])) {
            return $this->pool[$name] = new Client($this->configurations[$name]);
        }

        throw new \LogicException(
            sprintf('Predis configuration %s does not exist', $name)
        );
    }
}
