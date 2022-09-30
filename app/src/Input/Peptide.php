<?php

declare(strict_types=1);

namespace App\Input;

use Psr\Http\Message\ServerRequestInterface;

use Quanta\Validation;
use Quanta\Validation\Error;
use Quanta\Validation\Factory;
use Quanta\Validation\AbstractInput;
use Quanta\Validation\InvalidDataException;

use App\Input\Peptide\Type;
use App\Input\Peptide\Sequence;
use App\Input\Peptide\Affinity;
use App\Input\Peptide\Methods;
use App\Input\Peptide\Hotspots;

final class Peptide extends AbstractInput
{
    public static function fromRequest(ServerRequestInterface $request): self
    {
        $data = (array) $request->getParsedBody();

        return self::from($data);
    }

    protected static function validation(Factory $factory, Validation $v): Factory
    {
        return $factory->validation(
            $v->key('type')->string(Type::class),
            $v->key('sequence')->string(Sequence::class),
            $v->key('cter')->string(),
            $v->key('nter')->string(),
            $v->key('affinity')->array(Affinity::class),
            $v->key('hotspots')->array(Hotspots::class),
            $v->key('methods')->array(Methods::class),
            $v->key('info')->string(),
        );
    }

    public function __construct(
        public readonly Type $type,
        public readonly Sequence $sequence,
        public readonly string $cter,
        public readonly string $nter,
        public readonly Affinity $affinity,
        public readonly Hotspots $hotspots,
        public readonly Methods $methods,
        public readonly string $info,
    ) {
        foreach (array_keys($hotspots->data()) as $pos) {
            if ($pos >= strlen($this->sequence->value())) {
                throw new InvalidDataException(
                    Error::from('Hotspot position cannot be outsite sequence'),
                );
            }
        }
    }

    public function data(): array
    {
        return [
            'cter' => $this->cter,
            'nter' => $this->nter,
            'affinity' => $this->affinity->data(),
            'hotspots' => $this->hotspots->data(),
            'methods' => $this->methods->data(),
            'info' => $this->info,
        ];
    }
}
