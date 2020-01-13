<?php $this->layout('layout'); ?>
<div class="page-header">
    <h1>
        Welcome to drakkar!
        <small class="d-none d-md-inline">Lets sail toward the vinland!</small>
    </h1>
</div>

<div class="row">
    <div class="col">
        <a href="<?= $this->url('dataset', ['type' => Domain\Run::HH]) ?>" class="btn btn-primary btn-lg btn-block">
            Download current HH dataset
        </a>
    </div>
    <div class="col">
        <a href="<?= $this->url('dataset', ['type' => Domain\Run::VH]) ?>" class="btn btn-primary btn-lg btn-block">
            Download current VH dataset
        </a>
    </div>
</div>

<div class="row">
    <div class="col-4">
        <img src="<?= 'img/viking.png' ?>" class="img-fluid" style="width: 140px; opacity: 0.8;">
    </div>
    <div class="col">
        <div class="card">
            <h3 class="card-header">Ahoy mate, here is how to sail</h3>
            <div class="card-body">
                <ul class="my-0">
                    <li>
                        Select/discard publications for each curation run listed below
                    </li>
                    <li>
                        Add descriptions for each selected publication through the curation assistant form
                    </li>
                    <li>
                        Any time you can download the current vinland dataset
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<h2>Search publication</h2>
<?= $this->insert('publications/search/form') ?>

<h2>Curation runs</h2>
<?php if (count($runs) == 0): ?>
<p>
    There is no curation run.
</p>
<?php else: ?>
<table class="table">
    <thead>
        <tr>
            <th class="text-center col-1">Type</th>
            <th class="col-5">Name</th>
            <th class="text-center col-2">Date</th>
            <th class="text-center col-2">Publications</th>
            <th class="text-center col-2">-</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($runs as $run): ?>
        <tr>
            <td class="text-center">
                <?= $this->e($run['type']) ?>
            </td>
            <td>
                <?= $this->e($run['name']) ?>
            </td>
            <td class="text-center">
                <?= $this->e($run['created_at']) ?>
            </td>
            <td class="text-center">
                <span class="<?= $this->textclass($pending) ?>">
                    <?= $this->e($run['nbs'][$pending]) ?>
                </span>
                -
                <span class="<?= $this->textclass($selected) ?>">
                    <?= $this->e($run['nbs'][$selected]) ?>
                </span>
                -
                <span class="<?= $this->textclass($discarded) ?>">
                    <?= $this->e($run['nbs'][$discarded]) ?>
                </span>
                -
                <span class="<?= $this->textclass($curated) ?>">
                    <?= $this->e($run['nbs'][$curated]) ?>
                </span>
            </td>
            <td class="text-center">
                <a href="<?= $this->url('runs.publications.index', ['run_id' => $run['id']]) ?>">
                    Publications
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
