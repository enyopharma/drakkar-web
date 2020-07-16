<?php $this->layout('layout'); ?>

<?php $this->push('scripts'); ?>
<script type="text/javascript" src="<?= $this->asset('table.js') ?>"></script>
<script type="text/javascript">
    descriptions.table('descriptions-table', <?= json_encode([$description]) ?>);
</script>
<?php $this->end(); ?>

<div class="page-header">
    <h1>
        <a href="<?= $this->url('runs.index') ?>">Drakkar</a>
        &gt;
        <a href="<?= $this->url('runs.publications.index', $run) ?>">
            <?= $run['type'] ?> - <?= $run['name'] ?></a>
        &gt;
        <a href="<?= $publication['pmid'] ?>">
            <?= $publication['pmid'] ?>
        </a>
        &gt;
        <?= $description['stable_id'] ?>
    </h1>
</div>

<?= $this->insert('publications/card', [
    'run' => $run,
    'publication' => $publication,
    'source' => $this->url('runs.publications.descriptions.show', $description)
]) ?>

<h2 id="descriptions">
    Description with stable id <?= $description['stable_id'] ?>
</h2>

<div class="row">
    <div class="col">
        <div id="descriptions-table"></div>
    </div>
</div>
