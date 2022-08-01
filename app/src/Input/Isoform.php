<?php

declare(strict_types=1);

namespace App\Input;

use App\Input\Validation\ArrayKey;
use App\Input\Validation\ArrayFactory;

final class Isoform implements \JsonSerializable
{
    /**
     * @param mixed[] $data
     */
    public static function from(array $data): self
    {
        $factory = ArrayFactory::class(self::class)->validators(
            ArrayKey::required('accession')->string([Accession::class, 'from']),
            ArrayKey::required('occurrences')->array([OccurrenceList::class, 'from']),
        );

        return $factory($data);
    }

    public function __construct(
        public readonly Accession $accession,
        public readonly OccurrenceList $occurrences,
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return [
            'accession' => $this->accession->value,
            'occurrences' => $this->occurrences,
        ];
    }
}
