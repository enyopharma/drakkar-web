<?php $this->layout('layout'); ?>

<?php $this->push('scripts'); ?>
<script type="text/javascript" src="<?= $this->asset('build/form.js') ?>"></script>
<script type="text/javascript">
    $(document).ready(function () {
        form.create('form', '<?= $type ?>', <?= $description['run_id'] ?>, <?= $description['pmid'] ?>);
    })
</script>
<?php $this->end(); ?>

<div class="page-header">
    <h1>
        <a href="<?= $this->url('index') ?>">Drakkar</a>
        &gt;
        Description
    </h1>
</div>

<div id="form"></div>
