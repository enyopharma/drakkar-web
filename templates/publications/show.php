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

<h2 id="descriptions">
    Descriptions associated with this publication
</h2>

<?php if ($publication['state'] == $selected): ?>
<p>
    <a href="<?= $this->url('runs.publications.descriptions.create', $publication) ?>">
        Add new descriptions.
    </a>
</p>
<?php else: ?>
<p class="text-danger">
    Publication state must be 'selected' in order to add new descriptions.
</p>
<?php endif ?>

<?php if ($descriptions->count() == 0): ?>
<p>
    There is no descriptions associated with this publication.
</p>
<?php else: ?>
<?php $this->insert('pagination/nav', [
    'pagination' => $descriptions,
    'url' => $this->partialUrl('runs.publications.show', $publication, [], 'descriptions'),
]) ?>
<?php foreach ($descriptions as $description): ?>
<div class="row">
    <div class="col">
        <?= $this->insert('descriptions/card', ['description' => $description]) ?>
    </div>
</div>
<?php endforeach ?>
<?php $this->insert('pagination/nav', [
    'pagination' => $descriptions,
    'url' => $this->partialUrl('runs.publications.show', $publication, [], 'descriptions'),
]) ?>
<?php endif ?>
