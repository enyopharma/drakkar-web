<?php $this->layout('layout'); ?>
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
            Pending (<?= $run['nbs'][$this->pending()] ?>)
        </a>
    </li>
    <li class="nav-item">
        <a
            class="nav-link text-primary <?= $this->isSelected($state) ? 'active' : '' ?>"
            href="<?= $this->url('runs.show', $run, ['state' => $this->selected()]) ?>"
        >
            Selected (<?= $run['nbs'][$this->selected()] ?>)
        </a>
    </li>
    <li class="nav-item">
        <a
            class="nav-link text-danger <?= $this->isDiscarded($state) ? 'active' : '' ?>"
            href="<?= $this->url('runs.show', $run, ['state' => $this->discarded()]) ?>"
        >
            Discarded (<?= $run['nbs'][$this->discarded()] ?>)
        </a>
    </li>
    <li class="nav-item">
        <a
            class="nav-link text-success <?= $this->isCurated($state) ? 'active' : '' ?>"
            href="<?= $this->url('runs.show', $run, ['state' => $this->curated()]) ?>"
        >
            Curated (<?= $run['nbs'][$this->curated()] ?>)
        </a>
    </li>
</ul>

<?php $this->insert('runs/show/' . $state, ['run' => $run, 'publications' => $publications]) ?>
