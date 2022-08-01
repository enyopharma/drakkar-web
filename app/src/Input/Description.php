<?php

declare(strict_types=1);

namespace App\Input;

use Psr\Http\Message\ServerRequestInterface;

use App\Input\Validation\ArrayKey;
use App\Input\Validation\ArrayInput;
use App\Input\Validation\ArrayFactory;

final class Description extends ArrayInput
{
    public static function fromRequest(ServerRequestInterface $request): self
    {
        $data = (array) $request->getParsedBody();

        return self::from($data);
    }

    protected static function validation(ArrayFactory $factory): ArrayFactory
    {
        return $factory->validators(
            ArrayKey::required('stable_id')->string([StableId::class, 'from']),
            ArrayKey::required('method_id')->int([DatabaseId::class, 'from']),
            ArrayKey::required('interactor1')->array([Interactor::class, 'from']),
            ArrayKey::required('interactor2')->array([Interactor::class, 'from']),
        );
    }

    public function __construct(
        public readonly StableId $stable_id,
        public readonly DatabaseId $method_id,
        public readonly Interactor $interactor1,
        public readonly Interactor $interactor2,
    ) {
    }
}
