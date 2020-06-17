<?php
    $helpers = [
        App\Assertions\PublicationState::PENDING => [
            'header' => 'Pending publications',
            'empty' => 'There is no pending publication.',
        ],
        App\Assertions\PublicationState::SELECTED => [
            'header' => 'Selected publications',
            'empty' => 'There is no selected publication.',
        ],
        App\Assertions\PublicationState::DISCARDED => [
            'header' => 'Discarded publications',
            'empty' => 'There is no discarded publication.',
        ],
        App\Assertions\PublicationState::CURATED => [
            'header' => 'Curated publications',
            'empty' => 'There is no curated publication.',
        ],
    ];
?>

<?php
    $url = fn (int $page) => $this->url('runs.publications.index', $run,
        ['state' => $state, 'page' => $page, 'limit' => $limit],
        'publications',
    );
?>

<?php $this->layout('layout'); ?>

<div class="page-header">
    <h1>
        <a href="<?= $this->url('runs.index') ?>">Drakkar</a>
        &gt;
        Run <?= $run['name'] ?>
    </h1>
</div>

<h2 id="publications">
    <?= $helpers[$state]['header'] ?>
</h2>

<nav>
    <ul class="nav nav-tabs nav-fill">
        <li class="nav-item">
            <a
                class="nav-link text-warning <?= $state == App\Assertions\PublicationState::PENDING ? 'active' : '' ?>"
                href="<?= $this->url('runs.publications.index', $run, ['state' => App\Assertions\PublicationState::PENDING], 'publications') ?>"
            >
                Pending (<?= $run['nbs'][App\Assertions\PublicationState::PENDING] ?>)
            </a>
        </li>
        <li class="nav-item">
            <a
                class="nav-link text-primary <?= $state == App\Assertions\PublicationState::SELECTED ? 'active' : '' ?>"
                href="<?= $this->url('runs.publications.index', $run, ['state' => App\Assertions\PublicationState::SELECTED], 'publications') ?>"
            >
                Selected (<?= $run['nbs'][App\Assertions\PublicationState::SELECTED] ?>)
            </a>
        </li>
        <li class="nav-item">
            <a
                class="nav-link text-danger <?= $state == App\Assertions\PublicationState::DISCARDED ? 'active' : '' ?>"
                href="<?= $this->url('runs.publications.index', $run, ['state' => App\Assertions\PublicationState::DISCARDED], 'publications') ?>"
            >
                Discarded (<?= $run['nbs'][App\Assertions\PublicationState::DISCARDED] ?>)
            </a>
        </li>
        <li class="nav-item">
            <a
                class="nav-link text-success <?= $state == App\Assertions\PublicationState::CURATED ? 'active' : '' ?>"
                href="<?= $this->url('runs.publications.index', $run, ['state' => App\Assertions\PublicationState::CURATED], 'publications') ?>"
            >
                Curated (<?= $run['nbs'][App\Assertions\PublicationState::CURATED] ?>)
            </a>
        </li>
    </ul>
</nav>

<?php if (count($publications) == 0): ?>
<p>
    <?= $helpers[$state]['empty'] ?>
</p>
<?php else: ?>
<?= $this->pagination($total, $page, $limit, $url) ?>
<?php foreach($publications as $publication): ?>
<?php $this->insert('publications/card', [
    'run' => $run,
    'publication' => $publication,
    'source' => $this->url('runs.publications.index', $run,
        ['state' => $state, 'limit' => $limit],
        'publications',
    ),
]) ?>
<?php endforeach ?>
<?= $this->pagination($total, $page, $limit, $url) ?>
<?php endif ?>
