<?php $this->layout('layout'); ?>

<?php if ($publication['state'] == 'selected'): ?>
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
        <a href="<?= $url->generate('runs.index') ?>">
            Drakkar</a>
        &gt;
        <a href="<?= $url->generate('runs.publications.index', $run) ?>">
            <?= $run['type'] ?> - <?= $run['name'] ?></a>
        &gt;
        <a href="<?= $url->generate('runs.publications.descriptions.index', $publication) ?>">
            <?= $publication['pmid'] ?></a>
        &gt;
        new description
    </h1>
</div>

<?= $this->insert('publications/card', [
    'run' => $run,
    'publication' => $publication,
    'source' => count($description) == 0
        ? $url->generate('runs.publications.descriptions.create', $publication)
        : $url->generate('runs.publications.descriptions.edit', $description)
]) ?>

<?php if ($publication['state'] == 'selected'): ?>
<div id="description-form"></div>
<?php else: ?>
<p class="card-text text-warning">
    Publication state must be 'selected' in order to add new descriptions.
</p>
<?php endif ?>
