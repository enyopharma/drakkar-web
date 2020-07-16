<?php $this->layout('layout'); ?>

<div class="page-header">
    <h1>
        <a href="<?= $this->url('runs.index') ?>">Drakkar</a>
        &gt;
        Descriptions
    </h1>
</div>

<h2>Search descriptions</h2>

<?= $this->insert('descriptions/search/form', ['stable_id' => $stable_id]) ?>

<?php if (strlen(trim($stable_id)) == 0): ?>
<p>
    Please enter a stable id
</p>
<?php else: ?>
<p>
    No description found for stable id <?= $stable_id ?>
</p>
<?php endif ?>
