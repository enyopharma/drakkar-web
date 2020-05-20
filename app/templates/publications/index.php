<?php $this->layout('layout'); ?>

<div class="page-header">
    <h1>
        <a href="<?= $this->url('runs.index') ?>">Drakkar</a>
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
                class="nav-link <?= $this->textclass($pending) ?> <?= $state == $pending ? 'active' : '' ?>"
                href="<?= $this->url('runs.publications.index', $run['url'], ['state' => $pending], 'publications') ?>"
            >
                Pending (<?= $run['nbs'][$pending] ?>)
            </a>
        </li>
        <li class="nav-item">
            <a
                class="nav-link <?= $this->textclass($selected) ?> <?= $state == $selected ? 'active' : '' ?>"
                href="<?= $this->url('runs.publications.index', $run['url'], ['state' => $selected], 'publications') ?>"
            >
                Selected (<?= $run['nbs'][$selected] ?>)
            </a>
        </li>
        <li class="nav-item">
            <a
                class="nav-link <?= $this->textclass($discarded) ?> <?= $state == $discarded ? 'active' : '' ?>"
                href="<?= $this->url('runs.publications.index', $run['url'], ['state' => $discarded], 'publications') ?>"
            >
                Discarded (<?= $run['nbs'][$discarded] ?>)
            </a>
        </li>
        <li class="nav-item">
            <a
                class="nav-link <?= $this->textclass($curated) ?> <?= $state == $curated ? 'active' : '' ?>"
                href="<?= $this->url('runs.publications.index', $run['url'], ['state' => $curated], 'publications') ?>"
            >
                Curated (<?= $run['nbs'][$curated] ?>)
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
    'url' => function (int $page) use ($run, $state, $limit) {
        return $this->url('runs.publications.index',
            $run['url'],
            ['state' => $state, 'page' => $page, 'limit' => $limit],
            'publications'
        );
    },
]) ?>
<?php $this->insert('publications/deck', [
    'publications' => $publications,
    'source' => $this->url('runs.publications.index',
        $run['url'],
        ['state' => $state, 'limit' => $limit],
        'publications'
    ),
]) ?>
<?php $this->insert('pagination/nav', [
    'pagination' => $this->pagination($total, $page, $limit),
    'url' => function (int $page) use ($run, $state, $limit) {
        return $this->url('runs.publications.index',
            $run['url'],
            ['state' => $state, 'page' => $page, 'limit' => $limit],
            'publications'
        );
    },
]) ?>
<?php endif ?>