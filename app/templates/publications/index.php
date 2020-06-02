<?php
    $helpers = [
        'pending' => [
            'header' => 'Pending publications',
            'empty' => 'There is no pending publication.',
        ],
        'selected' => [
            'header' => 'Selected publications',
            'empty' => 'There is no selected publication.',
        ],
        'discarded' => [
            'header' => 'Discarded publications',
            'empty' => 'There is no discarded publication.',
        ],
        'curated' => [
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
                class="nav-link text-warning <?= $state == 'pending' ? 'active' : '' ?>"
                href="<?= $this->url('runs.publications.index', $run, ['state' => 'pending'], 'publications') ?>"
            >
                Pending (<?= $run['nbs']['pending'] ?>)
            </a>
        </li>
        <li class="nav-item">
            <a
                class="nav-link text-primary <?= $state == 'selected' ? 'active' : '' ?>"
                href="<?= $this->url('runs.publications.index', $run, ['state' => 'selected'], 'publications') ?>"
            >
                Selected (<?= $run['nbs']['selected'] ?>)
            </a>
        </li>
        <li class="nav-item">
            <a
                class="nav-link text-danger <?= $state == 'discarded' ? 'active' : '' ?>"
                href="<?= $this->url('runs.publications.index', $run, ['state' => 'discarded'], 'publications') ?>"
            >
                Discarded (<?= $run['nbs']['discarded'] ?>)
            </a>
        </li>
        <li class="nav-item">
            <a
                class="nav-link text-success <?= $state == 'curated' ? 'active' : '' ?>"
                href="<?= $this->url('runs.publications.index', $run, ['state' => 'curated'], 'publications') ?>"
            >
                Curated (<?= $run['nbs']['curated'] ?>)
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
<?php $this->insert('publications/deck', [
    'publications' => $publications,
    'source' => $this->url('runs.publications.index', $run,
        ['state' => $state, 'limit' => $limit],
        'publications',
    ),
]) ?>
<?= $this->pagination($total, $page, $limit, $url) ?>
<?php endif ?>
