<?php $this->layout('layout'); ?>

<div class="page-header">
    <h1>
        <a href="<?= $url->generate('runs.index') ?>">Drakkar</a>
        &gt;
        Run <?= $run['name'] ?>
    </h1>
</div>

<h2 id="publications">
    <?= $this->header($state) ?>
</h2>

<nav>
    <ul class="nav nav-tabs nav-fill">
        <li class="nav-item">
            <a
                class="nav-link <?= $this->textclass('pending') ?> <?= $state == 'pending' ? 'active' : '' ?>"
                href="<?= $url->generate('runs.publications.index', $run, ['state' => 'pending'], 'publications') ?>"
            >
                Pending (<?= $run['nbs']['pending'] ?>)
            </a>
        </li>
        <li class="nav-item">
            <a
                class="nav-link <?= $this->textclass('selected') ?> <?= $state == 'selected' ? 'active' : '' ?>"
                href="<?= $url->generate('runs.publications.index', $run, ['state' => 'selected'], 'publications') ?>"
            >
                Selected (<?= $run['nbs']['selected'] ?>)
            </a>
        </li>
        <li class="nav-item">
            <a
                class="nav-link <?= $this->textclass('discarded') ?> <?= $state == 'discarded' ? 'active' : '' ?>"
                href="<?= $url->generate('runs.publications.index', $run, ['state' => 'discarded'], 'publications') ?>"
            >
                Discarded (<?= $run['nbs']['discarded'] ?>)
            </a>
        </li>
        <li class="nav-item">
            <a
                class="nav-link <?= $this->textclass('curated') ?> <?= $state == 'curated' ? 'active' : '' ?>"
                href="<?= $url->generate('runs.publications.index', $run, ['state' => 'curated'], 'publications') ?>"
            >
                Curated (<?= $run['nbs']['curated'] ?>)
            </a>
        </li>
    </ul>
</nav>

<?php if (count($publications) == 0): ?>
<p>
    <?= $this->empty($state) ?>
</p>
<?php else: ?>
<?php $this->insert('pagination/nav', [
    'pagination' => $this->pagination($total, $page, $limit),
    'url' => fn (int $page) => $url->generate('runs.publications.index',
        $run,
        ['state' => $state, 'page' => $page, 'limit' => $limit],
        'publications',
    ),
]) ?>
<?php $this->insert('publications/deck', [
    'run' => $run,
    'publications' => $publications,
    'source' => $url->generate('runs.publications.index', $run, ['state' => $state, 'limit' => $limit], 'publications'),
]) ?>
<?php $this->insert('pagination/nav', [
    'pagination' => $this->pagination($total, $page, $limit),
    'url' => fn (int $page) => $url->generate('runs.publications.index',
        $run,
        ['state' => $state, 'page' => $page, 'limit' => $limit],
        'publications',
    ),
]) ?>
<?php endif ?>
