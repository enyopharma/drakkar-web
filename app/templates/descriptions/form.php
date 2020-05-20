<?php $this->layout('layout'); ?>

<?php if ($publication['state'] == $selected): ?>
<?php $this->push('scripts'); ?>
<script type="text/javascript" src="<?= $this->asset('form.js') ?>"></script>
<script type="text/javascript">
    descriptions.form(
        'description-form',
        '<?= $run['type'] ?>',
        <?= $run['id'] ?>,
        <?= $publication['pmid'] ?>,
        <?= count($description) == 0 ? 'null' : json_encode($description) ?>
    );
</script>
<?php $this->end(); ?>
<?php endif ?>

<div class="page-header">
    <h1>
        <a href="<?= $this->url('runs.index') ?>">
            Drakkar</a>
        &gt;
        <a href="<?= $this->url('runs.publications.index', $run['url']) ?>">
            <?= $run['type'] ?> - <?= $run['name'] ?></a>
        &gt;
        <a href="<?= $this->url('runs.publications.descriptions.index', $publication['url']) ?>">
            <?= $publication['pmid'] ?></a>
        &gt;
        new description
    </h1>
</div>

<?= $this->insert('publications/card', [
    'publication' => $publication,
    'source' => count($description) == 0
        ? $this->url('runs.publications.descriptions.create', $publication)
        : $this->url('runs.publications.descriptions.edit', $description)
]) ?>

<?php if ($publication['state'] == $selected): ?>
<div id="description-form"></div>
<?php else: ?>
<p class="card-text text-warning">
    Publication state must be 'selected' in order to add new descriptions.
</p>
<?php endif ?>