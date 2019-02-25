<?php $this->layout('layout'); ?>
<div class="page-header">
    <h1>
        Welcome to drakkar!
        <small class="d-none d-md-inline">Lets sail toward the vinland!</small>
    </h1>
</div>
<div class="row">
    <div class="col">
        <a href="#" class="btn btn-primary btn-lg btn-block">
            Download current vinland dataset
        </a>
    </div>
</div>
<div class="row">
    <div class="col-4 d-none d-md-block">
        <img src="<?= 'img/viking.png' ?>" class="img-fluid" style="width: 200px; opacity: 0.8;">
    </div>
    <div class="col offset-md-1">
        <div class="card">
            <h3 class="card-header">Ahoy mate, here is how to sail</h3>
            <div class="card-body">
                <ul>
                    <li>
                        Login to <strong>Drakkar</strong> with you ENYOpharma account
                    </li>
                    <li>
                        Review publications from each curation run and discard the unrelevant ones
                    </li>
                    <li>
                        Add descriptions for each relevant publication through the curation assistant form
                    </li>
                    <li>
                        Any time you can download the current vinland dataset
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<h2>Curation runs</h2>
<table class="table">
    <thead>
        <tr>
            <th class="text-center col-1">N°</th>
            <th class="text-center col-1">Type</th>
            <th class="col-4">Description</th>
            <th class="text-center col-2">Date</th>
            <th class="text-center col-2">Publications</th>
            <th class="text-center col-2">-</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($runs as $run): ?>
        <tr>
            <td class="text-center">
                <?= $this->e($run['name']) ?>
            </td>
            <td class="text-center">
                <?= $this->e($run['type']) ?>
            </td>
            <td>
                <?= $this->e($run['description']) ?>
            </td>
            <td class="text-center">
                <?= $this->e($run['created_at']) ?>
            </td>
            <td class="text-center">
                <span title="curated/predurated/total">
                    <?= $this->e($run['nb_curated']) ?> /
                    <?= $this->e($run['nb_precurated']) ?> /
                    <?= $this->e($run['nb_total']) ?>
                </span>
            </td>
            <td class="text-center">
                <a href="<?= $this->url('runs.show', $run) ?>">
                    Resume curation
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
