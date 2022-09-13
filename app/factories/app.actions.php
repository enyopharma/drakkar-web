<?php

declare(strict_types=1);

use App\Actions\StorePeptideInterface;
use App\Actions\StoreDescriptionInterface;
use App\Actions\DeleteDescriptionInterface;
use App\Actions\UpdatePublicationStateInterface;

return [
    StorePeptideInterface::class => App\Actions\StorePeptideSql::class,
    StoreDescriptionInterface::class => App\Actions\StoreDescriptionSql::class,
    DeleteDescriptionInterface::class => App\Actions\DeleteDescriptionSql::class,
    UpdatePublicationStateInterface::class => App\Actions\UpdatePublicationStateSql::class,
];
