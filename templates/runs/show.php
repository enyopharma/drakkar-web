<?php $this->layout('layout'); ?>

<?php $this->push('scripts'); ?>
<script type="text/javascript">
$(document).ready(function () {
    $('.card .collapse').on('shown.bs.collapse', function (e) {
        var textarea = $(e.target).find('textarea');
        var value = textarea.val();

        // trick to put the cursor at the end of the textarea content.
        textarea.focus().val('').val(value);
    });
});
</script>
<?php $this->end(); ?>

<?php if ($state == $pending): ?>
<?php $this->push('scripts'); ?>
<script type="text/javascript">
$(document).ready(function () {
    $('.card .collapse').first().collapse('show');
});
</script>
<?php $this->end(); ?>
<?php endif; ?>

<div class="page-header">
    <h1>
        <a href="<?= $this->url('index') ?>">Drakkar</a>
        &gt;
        Run <?= $run['name'] ?>
    </h1>
</div>

<ul class="nav nav-tabs nav-fill my-4">
    <li class="nav-item">
        <a
            class="nav-link text-warning <?= $state == $pending ? 'active' : '' ?>"
            href="<?= $this->url('runs.show', $run, ['state' => $pending]) ?>"
        >
            Pending (<?= $run['nbs'][$pending] ?>)
        </a>
    </li>
    <li class="nav-item">
        <a
            class="nav-link text-primary <?= $state == $selected ? 'active' : '' ?>"
            href="<?= $this->url('runs.show', $run, ['state' => $selected]) ?>"
        >
            Selected (<?= $run['nbs'][$selected] ?>)
        </a>
    </li>
    <li class="nav-item">
        <a
            class="nav-link text-danger <?= $state == $discarded ? 'active' : '' ?>"
            href="<?= $this->url('runs.show', $run, ['state' => $discarded]) ?>"
        >
            Discarded (<?= $run['nbs'][$discarded] ?>)
        </a>
    </li>
    <li class="nav-item">
        <a
            class="nav-link text-success <?= $state == $curated ? 'active' : '' ?>"
            href="<?= $this->url('runs.show', $run, ['state' => $curated]) ?>"
        >
            Curated (<?= $run['nbs'][$curated] ?>)
        </a>
    </li>
</ul>

<h2 class="my-4">
    <?= $this->header($state) ?>
</h2>

<?php if ($run['publications']->count() == 0): ?>
<p>
    <?= $this->empty($state) ?>
</p>
<?php else: ?>
<?php $this->insert('pagination/nav', [
    'pagination' => $run['publications'],
    'url' => $this->partialUrl('runs.show', $run, ['state' => $state]),
]) ?>
<div class="row my-4">
    <div class="col">
        <?php $this->insert('publications/deck', [
            'type' => $run['type'],
            'publications' => $run['publications'],
        ]) ?>
    </div>
</div>
<?php $this->insert('pagination/nav', [
    'pagination' => $run['publications'],
    'url' => $this->partialUrl('runs.show', $run, ['state' => $state]),
]) ?>
<?php endif ?>
