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

<?php if ($this->isPending($state)): ?>
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
            class="nav-link text-warning <?= $this->isPending($state) ? 'active' : '' ?>"
            href="<?= $this->url('runs.show', $run, ['state' => $this->pending()]) ?>"
        >
            Pending (<?= $nbs[$this->pending()] ?>)
        </a>
    </li>
    <li class="nav-item">
        <a
            class="nav-link text-primary <?= $this->isSelected($state) ? 'active' : '' ?>"
            href="<?= $this->url('runs.show', $run, ['state' => $this->selected()]) ?>"
        >
            Selected (<?= $nbs[$this->selected()] ?>)
        </a>
    </li>
    <li class="nav-item">
        <a
            class="nav-link text-danger <?= $this->isDiscarded($state) ? 'active' : '' ?>"
            href="<?= $this->url('runs.show', $run, ['state' => $this->discarded()]) ?>"
        >
            Discarded (<?= $nbs[$this->discarded()] ?>)
        </a>
    </li>
    <li class="nav-item">
        <a
            class="nav-link text-success <?= $this->isCurated($state) ? 'active' : '' ?>"
            href="<?= $this->url('runs.show', $run, ['state' => $this->curated()]) ?>"
        >
            Curated (<?= $nbs[$this->curated()] ?>)
        </a>
    </li>
</ul>

<h2 class="my-4">
    <?= $this->stateMap($state)['header'] ?>
</h2>

<?php if ($publications->count() == 0): ?>
<p>
    <?= $this->stateMap($state)['empty'] ?>
</p>
<?php else: ?>
<?php $this->insert('pagination/nav', [
    'pagination' => $publications,
    'url' => $this->partialUrl('runs.show', $run, ['state' => $state]),
]) ?>
<div class="row my-4">
    <div class="col">
        <?php $this->insert('publications/deck', [
            'type' => $run['type'],
            'publications' => $publications,
        ]) ?>
    </div>
</div>
<?php $this->insert('pagination/nav', [
    'pagination' => $publications,
    'url' => $this->partialUrl('runs.show', $run, ['state' => $state]),
]) ?>
<?php endif ?>
