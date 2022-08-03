<?php

declare(strict_types=1);

namespace App\Input;

use Psr\Http\Message\ServerRequestInterface;

use App\Input\Validation\ArrayKey;
use App\Input\Validation\ArrayInput;
use App\Input\Validation\ArrayFactory;

use App\Input\Peptide\Type;
use App\Input\Peptide\Sequence;
use App\Input\Peptide\Affinity;
use App\Input\Peptide\Methods;
use App\Input\Peptide\Hotspots;
use App\Input\Validation\InvalidDataException;

final class Peptide extends ArrayInput
{
    public static function fromRequest(ServerRequestInterface $request): self
    {
        $data = (array) $request->getParsedBody();

        return self::from($data);
    }

    protected static function validation(ArrayFactory $factory): ArrayFactory
    {
        return $factory->validators(
            ArrayKey::required('type')->string([Type::class, 'from']),
            ArrayKey::required('sequence')->string([Sequence::class, 'from']),
            ArrayKey::required('cter')->string(),
            ArrayKey::required('nter')->string(),
            ArrayKey::required('affinity')->array([Affinity::class, 'from']),
            ArrayKey::required('hotspots')->array([Hotspots::class, 'from']),
            ArrayKey::required('methods')->array([Methods::class, 'from']),
            ArrayKey::required('info')->string(),
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
        foreach (array_keys($hotspots->value) as $pos) {
            if ($pos >= strlen($this->sequence->value)) {
                throw InvalidDataException::error('Hotspot position cant be outsite sequence');
            }
        }
    }

    public function data(): array
    {
        return [
            'cter' => $this->cter,
            'nter' => $this->nter,
            'affinity' => $this->affinity,
            'hotspots' => $this->hotspots,
            'methods' => $this->methods,
            'info' => $this->info,
        ];
    }
}
