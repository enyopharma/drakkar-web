<?php $this->layout('layout'); ?>

<?php
$source = match ($type) {
    'create' => $this->url('runs.publications.descriptions.create', $publication + ['type' => 'create']),
    'copy' => $this->url('runs.publications.descriptions.edit', $description + ['type' => 'copy']),
    'edit' => $this->url('runs.publications.descriptions.edit', $description + ['type' => 'edit']),
};
?>

<?php
$display = $type == 'edit' || $publication['state'] == App\Assertions\PublicationState::SELECTED;
?>

<?php if ($display) : ?>
    <?php $this->push('scripts'); ?>
    <script type="text/javascript" src="<?= $this->asset('react-dom.js') ?>"></script>
    <script type="text/javascript" src="<?= $this->asset('form.js') ?>"></script>
    <script type="text/javascript">
        form(
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
        New description
    </h1>
</div>

<?= $this->insert('publications/card', [
    'run' => $run,
    'publication' => $publication,
    'source' => $source,
]) ?>

<?php if ($display) : ?>
    <?php if ($type == 'edit') : ?>
        <p>
            <a href="<?= $this->url('runs.publications.descriptions.peptides.index', $description) ?>" class="btn btn-block btn-primary">
                Edit peptides info
            </a>
        </p>
    <?php endif; ?>
    <div id="description-form"></div>
<?php else : ?>
    <p class="card-text text-warning">
        Publication state must be 'selected' in order to add new descriptions.
    </p>
<?php endif ?>
