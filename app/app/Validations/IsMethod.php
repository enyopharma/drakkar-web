<?php

declare(strict_types=1);

namespace App\Validations;

use Quanta\Validation\Is;
use Quanta\Validation\Error;
use Quanta\Validation\Field;
use Quanta\Validation\InputInterface;
use Quanta\Validation\Rules\OfType;
use Quanta\Validation\Rules\NotEmpty;
use Quanta\Validation\Rules\Matching;

final class IsMethod
{
    const PSIMI_ID_PATTERN = '/^MI:[0-9]{4}$/';

    private DataSource $source;

    public function __construct(DataSource $source)
    {
        $this->source = $source;
    }

    public function __invoke(array $data): InputInterface
    {
        $methodExists = \Closure::fromCallable([$this, 'methodExists']);

        $isStr = new Is(new OfType('string'));
        $isNotEmpty = new Is(new NotEmpty);
        $isPsimiId = new Is(new Matching(self::PSIMI_ID_PATTERN));
        $isMethod = new Is($methodExists);

        $validate = Field::required('psimi_id', $isStr, $isNotEmpty, $isPsimiId, $isMethod);

        return $validate($data);
    }

    private function methodExists(string $psimi_id): array
    {
        $method = $this->source->method($psimi_id);

        return $method !== false ? [] : [
            new Error(sprintf('no method with psimi id %s', $psimi_id))
        ];
    }
}
