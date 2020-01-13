<?php $this->layout('layout'); ?>

<div class="page-header">
    <h1>
        <a href="<?= $this->url('runs.index') ?>">Drakkar</a>
        &gt;
        Publications
    </h1>
</div>

<h2>Search publications</h2>

<?= $this->insert('publications/search/form', ['pmid' => $pmid]) ?>

<?php foreach ($publications as $publication): ?>
<?php $this->insert('publications/card', ['publication' => $publication, 'source' => $this->url('publications.index', [], [
    'pmid' => $publication['pmid'],
])]) ?>
<?php endforeach; ?>
