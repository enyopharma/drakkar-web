<?php

declare(strict_types=1);

namespace Domain\ReadModel;

final class ViewRepository
{
    private $runs;
    private $publications;
    private $descriptions;
    private $methods;
    private $proteins;
    private $dataset;

    public function __construct(
        RunViewInterface $runs,
        PublicationViewInterface $publications,
        DescriptionViewInterface $descriptions,
        MethodViewInterface $methods,
        ProteinViewInterface $proteins,
        DatasetViewInterface $dataset
    ) {
        $this->runs = $runs;
        $this->publications = $publications;
        $this->descriptions = $descriptions;
        $this->methods = $methods;
        $this->proteins = $proteins;
        $this->dataset = $dataset;
    }

    public function runs(): RunViewInterface
    {
        return $this->runs;
    }

    public function publications(): PublicationViewInterface
    {
        return $this->publications;
    }

    public function descriptions(): DescriptionViewInterface
    {
        return $this->descriptions;
    }

    public function methods(): MethodViewInterface
    {
        return $this->methods;
    }

    public function proteins(): ProteinViewInterface
    {
        return $this->proteins;
    }

    public function dataset(): DatasetViewInterface
    {
        return $this->dataset;
    }
}
