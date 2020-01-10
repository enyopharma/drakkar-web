<?php $this->layout('layout'); ?>

<?php $this->push('scripts'); ?>
<script type="text/javascript" src="<?= $this->asset('search.js') ?>"></script>
<script type="text/javascript">
    search.form('#search', <?= $run['id'] ?>);
</script>
<?php $this->end(); ?>

<div class="page-header">
    <h1>
        <a href="<?= $this->url('runs.index') ?>">Drakkar</a>
        &gt;
        Run <?= $run['name'] ?>
    </h1>
</div>

<h2>Search publications</h2>

<form id="search" action="#">
    <div class="row">
        <div class="col-4">
            <input type="text" class="pmid form-control" placeholder="PMID" />
        </div>
        <div class="col-2">
            <button type="submit" class="btn btn-block btn-primary">
                Search publication
            </button>
        </div>
    </div>
</form>

<h2 id="publications">
    <?= $this->header($state) ?>
</h2>

<nav>
    <ul class="nav nav-tabs nav-fill">
        <li class="nav-item">
            <a
                class="nav-link <?= $this->textclass($pending) ?> <?= $state == $pending ? 'active' : '' ?>"
                href="<?= $this->url('runs.publications.index', ['run_id' => $run['id']], ['state' => $pending], 'publications') ?>"
            >
                Pending (<?= $run['nbs'][$pending] ?>)
            </a>
        </li>
        <li class="nav-item">
            <a
                class="nav-link <?= $this->textclass($selected) ?> <?= $state == $selected ? 'active' : '' ?>"
                href="<?= $this->url('runs.publications.index', ['run_id' => $run['id']], ['state' => $selected], 'publications') ?>"
            >
                Selected (<?= $run['nbs'][$selected] ?>)
            </a>
        </li>
        <li class="nav-item">
            <a
                class="nav-link <?= $this->textclass($discarded) ?> <?= $state == $discarded ? 'active' : '' ?>"
                href="<?= $this->url('runs.publications.index', ['run_id' => $run['id']], ['state' => $discarded], 'publications') ?>"
            >
                Discarded (<?= $run['nbs'][$discarded] ?>)
            </a>
        </li>
        <li class="nav-item">
            <a
                class="nav-link <?= $this->textclass($curated) ?> <?= $state == $curated ? 'active' : '' ?>"
                href="<?= $this->url('runs.publications.index', ['run_id' => $run['id']], ['state' => $curated], 'publications') ?>"
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
