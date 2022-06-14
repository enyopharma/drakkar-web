<?php $this->layout('layout'); ?>

<?php $this->push('scripts'); ?>
<script type="text/javascript" src="<?= $this->asset('react-dom.js') ?>"></script>
<script type="text/javascript" src="<?= $this->asset('peptides.js') ?>"></script>
<script type="text/javascript">
    peptides('peptides', <?= json_encode($peptides) ?>);
</script>
<?php $this->end(); ?>

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
        <a href="<?= $this->url('runs.publications.descriptions.index', $publication, ['stable_id' => $description['stable_id']]) ?>">
            <?= $description['stable_id'] ?></a>
        &gt;
        Peptides
    </h1>
</div>

<?= $this->insert('publications/card', [
    'run' => $run,
    'publication' => $publication,
    'source' => $this->url('runs.publications.descriptions.peptides.index', $description),
]) ?>

<div id="peptides"></div>
