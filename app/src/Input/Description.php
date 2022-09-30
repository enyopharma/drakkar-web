<?php

declare(strict_types=1);

namespace App\Input;

use Psr\Http\Message\ServerRequestInterface;

use Quanta\Validation;
use Quanta\Validation\Factory;
use Quanta\Validation\AbstractInput;

use App\Input\Description\StableId;
use App\Input\Description\DatabaseId;
use App\Input\Description\Interactor;

final class Description extends AbstractInput
{
    protected static function validation(Factory $factory, Validation $v): Factory
    {
        return $factory->validation(
            $v->key('stable_id')->string(StableId::class),
            $v->key('method_id')->int(DatabaseId::class),
            $v->key('interactor1')->array(Interactor::class),
            $v->key('interactor2')->array(Interactor::class),
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
