<?php $this->layout('layout'); ?>

<?php $this->push('scripts'); ?>
<script type="text/javascript" src="<?= $this->asset('build/form.js') ?>"></script>
<script type="text/javascript">
    $(document).ready(function () {
        form.create('description', '<?= $publication['run']['type'] ?>', <?= $publication['run']['id'] ?>, <?= $publication['pmid'] ?>);
    })
</script>
<?php $this->end(); ?>

<div class="page-header">
    <h1>
        <a href="<?= $this->url('index') ?>">
            Drakkar</a>
        &gt;
        <a href="<?= $this->url('runs.show', $publication['run']) ?>">
            <?= $publication['run']['type'] ?> - <?= $publication['run']['name'] ?></a>
        &gt;
        <a href="#">
            <?= $publication['pmid'] ?></a>
        &gt;
        new description
    </h1>
</div>

<div id="description"></div>
