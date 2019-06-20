<?php $this->layout('layout'); ?>

<div class="page-header">
    <h1>
        <a href="<?= $this->url('runs.index') ?>">Drakkar</a>
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
</ul>

<h2 id="publications" class="my-4">
    <?= $this->header($state) ?>
</h2>

<?php if ($publications->count() == 0): ?>
<p>
    <?= $this->empty($state) ?>
</p>
<?php else: ?>
<?php $this->insert('pagination/nav', [
    'pagination' => $publications,
    'url' => $this->partialUrl('runs.show', $run, ['state' => $state], 'publications'),
]) ?>
<div class="row my-4">
    <div class="col">
        <?php $this->insert('publications/deck', [
            'type' => $run['type'],
            'publications' => $publications,
            'redirect' => $this->partialUrl('runs.show', $run, ['state' => $state], 'publications'),
        ]) ?>
    </div>
</div>
<?php $this->insert('pagination/nav', [
    'pagination' => $publications,
    'url' => $this->partialUrl('runs.show', $run, ['state' => $state], 'publications'),
]) ?>
<?php endif ?>
