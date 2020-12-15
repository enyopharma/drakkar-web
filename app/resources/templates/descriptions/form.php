<?php $this->layout('layout'); ?>

<?php
    $display = $type == 'edit' || $publication['state'] == App\Assertions\PublicationState::SELECTED;
?>

<?php if ($display): ?>
<?php $this->push('scripts'); ?>
<script type="text/javascript" src="<?= $this->asset('react-dom.js') ?>"></script>
<script type="text/javascript" src="<?= $this->asset('form.js') ?>"></script>
<script type="text/javascript">
    Drakkar.form(
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
        <a href="<?= $this->url('runs.index') ?>">Drakkar</a>
        &gt;
        <a href="<?= $this->url('runs.publications.index', $run) ?>">
            <?= $run['type'] ?> - <?= $run['name'] ?></a>
        &gt;
        <a href="<?= $this->url('runs.publications.descriptions.index', $publication) ?>">
            <?= $publication['pmid'] ?></a>
        &gt;
        new description
    </h1>
</div>

<?php
    $source = [
        'create' => ['runs.publications.descriptions.create', $publication],
        'copy' => ['runs.publications.descriptions.copy', $description],
        'edit' => ['runs.publications.descriptions.edit', $description],
    ];
?>

<?= $this->insert('publications/card', [
    'run' => $run,
    'publication' => $publication,
    'source' => $this->url(...($source[$type] ?? [])),
]) ?>

<?php if ($display): ?>
<div id="description-form"></div>
<?php else: ?>
<p class="card-text text-warning">
    Publication state must be 'selected' in order to add new descriptions.
</p>
<?php endif ?>
