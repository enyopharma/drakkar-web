<?php $this->layout('layout'); ?>

<div class="page-header">
    <h1>
        <a href="<?= $this->url('runs.index') ?>">Drakkar</a>
        &gt;
        Run <?= $run['name'] ?>
    </h1>
</div>

<ul class="nav nav-tabs nav-fill my-4">
    <li class="nav-item">
        <a
            class="nav-link <?= $this->textclass($pending) ?> <?= $state == $pending ? 'active' : '' ?>"
            href="<?= $this->url('runs.publications.index', ['run_id' => $run['id']], ['state' => $pending]) ?>"
        >
            Pending (<?= $run['nbs'][$pending] ?>)
        </a>
    </li>
    <li class="nav-item">
        <a
            class="nav-link <?= $this->textclass($selected) ?> <?= $state == $selected ? 'active' : '' ?>"
            href="<?= $this->url('runs.publications.index', ['run_id' => $run['id']], ['state' => $selected]) ?>"
        >
            Selected (<?= $run['nbs'][$selected] ?>)
        </a>
    </li>
    <li class="nav-item">
        <a
            class="nav-link <?= $this->textclass($discarded) ?> <?= $state == $discarded ? 'active' : '' ?>"
            href="<?= $this->url('runs.publications.index', ['run_id' => $run['id']], ['state' => $discarded]) ?>"
        >
            Discarded (<?= $run['nbs'][$discarded] ?>)
        </a>
    </li>
    <li class="nav-item">
        <a
            class="nav-link <?= $this->textclass($curated) ?> <?= $state == $curated ? 'active' : '' ?>"
            href="<?= $this->url('runs.publications.index', ['run_id' => $run['id']], ['state' => $curated]) ?>"
        >
            Curated (<?= $run['nbs'][$curated] ?>)
        </a>
    </li>
</ul>

<h2 id="publications" class="my-4">
    <?= $this->header($state) ?>
</h2>

<?php if (count($publications) == 0): ?>
<p>
    <?= $this->empty($state) ?>
</p>
<?php else: ?>
<?php ['total' => $total, 'page' => $page, 'limit' => $limit] = $pagination ?>
<?php $this->insert('pagination/nav', [
    'pagination' => $this->pagination($total, $page, $limit),
    'url' => function (int $page) use ($run, $state, $limit) {
        return $this->url(
            'runs.publications.index',
            ['run_id' => $run['id']],
            ['state' => $state, 'page' => $page, 'limit' => $limit],
            'publications'
        );
    },
]) ?>
<?php $this->insert('publications/deck', [
    'publications' => $publications,
    'source' => $this->url(
        'runs.publications.index',
        ['run_id' => $run['id']],
        ['state' => $state, 'limit' => $limit],
        'publications'
    ),
]) ?>
<?php $this->insert('pagination/nav', [
    'pagination' => $this->pagination($total, $page, $limit),
    'url' => function (int $page) use ($run, $state, $limit) {
        return $this->url(
            'runs.publications.index',
            ['run_id' => $run['id']],
            ['state' => $state, 'page' => $page, 'limit' => $limit],
            'publications'
        );
    },
]) ?>
<?php endif ?>
