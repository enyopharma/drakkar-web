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

<?php if (strlen(trim($pmid)) == 0): ?>
<p>
    Please enter a pmid
</p>
<?php elseif (count($publications) == 0): ?>
<p>
    No publication found for pmid <?= $pmid ?>
</p>
<?php else: ?>
<p>
    <?= count($publications) == 1 ? '1 publication' : count($publications) . ' publications' ?> found for pmid <?= $pmid ?>
</p>
<?php foreach ($publications as $publication): ?>
<?php $this->insert('publications/card', ['publication' => $publication, 'source' => $this->url('publications.index', [], [
    'pmid' => $publication['pmid'],
])]) ?>
<?php endforeach; ?>
<?php endif; ?>
