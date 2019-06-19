<?php $this->layout('layout'); ?>

<?php if ($publication['state'] == $selected): ?>
<?php $this->push('scripts'); ?>
<script type="text/javascript" src="<?= $this->asset('build/form.js') ?>"></script>
<script type="text/javascript">
    $(document).ready(function () {
        description.form(
            'description-wrap',
            'description-form',
            '<?= $publication['run']['type'] ?>',
            <?= $publication['run']['id'] ?>,
            <?= $publication['pmid'] ?>,
            <?= count($description) == 0 ? '{}' : json_encode($description) ?>
        );
    })
</script>
<?php $this->end(); ?>
<?php endif ?>

<div class="page-header">
    <h1>
        <a href="<?= $this->url('runs.index') ?>">
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

<div class="row">
    <div class="col">
        <?= $this->insert('publications/card', [
            'publication' => $publication,
            'redirect' => count($description) == 0
                ? $this->url('runs.publications.descriptions.create', $publication)
                : $this->url('runs.publications.descriptions.edit', $description)
        ]) ?>
    </div>
</div>

<hr class="mt-4 mb-4">

<?php if ($publication['state'] == $selected): ?>
<div id="description-wrap" class="row">
    <div class="col">
        <div class="card">
            <h4 class="card-header">
                Add a new description
            </h4>
            <div class="card-body">
                <div id="description-form"></div>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="alert alert-danger">
    Publication state must be 'selected' in order to add new descriptions.
</div>
<?php endif ?>
