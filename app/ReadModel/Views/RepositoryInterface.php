<?php

declare(strict_types=1);

namespace App\ReadModel\Views;

interface RepositoryInterface
{
    public function methods(): MethodViewInterface;

    public function proteins(): ProteinViewInterface;

    public function runs(): RunViewInterface;

    public function publications(): PublicationViewInterface;

    public function dataset(): DatasetViewInterface;
}
