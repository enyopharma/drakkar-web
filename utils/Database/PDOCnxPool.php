<?php declare(strict_types=1);

namespace Utils\Database;

final class PDOCnxPool
{
    private $configurations;

    private $pool;

    public function __construct(array $configurations)
    {
        $this->configurations = $configurations;
        $this->pool = [];
    }

    public function cnx(string $name): \PDO
    {
        if (isset($this->pool[$name])) {
            return $this->pool[$name];
        }

        if (isset($this->configurations[$name])) {
            $dsn = $this->configurations[$name]['dsn'] ?? '';

            if (isset($this->configurations[$name]['username'])) {
                $xs[] = $this->configurations[$name]['username'];
            }

            if (isset($this->configurations[$name]['password'])) {
                $xs[] = $this->configurations[$name]['password'];
            }

            if (isset($this->configurations[$name]['options'])) {
                $xs[] = $this->configurations[$name]['options'];
            }

            return $this->pool[$name] = new \PDO($dsn, ...$xs);
        }

        throw new \LogicException(
            sprintf('PDO configuration %s does not exist', $name)
        );
    }
}
