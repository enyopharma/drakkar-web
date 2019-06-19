<?php $this->layout('layout'); ?>

<div class="page-header">
    <h1>
        <a href="<?= $this->url('runs.index') ?>">
            Drakkar</a>
        &gt;
        <a href="<?= $this->url('runs.show', $publication['run']) ?>">
            <?= $publication['run']['type'] ?> - <?= $publication['run']['name'] ?></a>
        &gt;
        <?= $publication['pmid'] ?>
    </h1>
</div>

<div class="row">
    <div class="col">
        <?= $this->insert('publications/card', [
            'publication' => $publication,
            'redirect' => $this->url('runs.publications.show', $publication)
        ]) ?>
    </div>
</div>

<div id="description-wrap" class="row">
    <div class="col">
        <div class="card">
            <h3 class="card-header">
                Descriptions from this publication
            </h3>
            <div class="card-body">
                <?php if ($publication['state'] == $selected): ?>
                <a href="<?= $this->url('runs.publications.descriptions.create', $publication) ?>">
                    Add new descriptions
                </a>
                <?php else: ?>
                <div class="text-danger">
                    Publication state must be 'selected' in order to add new descriptions.
                </div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>
