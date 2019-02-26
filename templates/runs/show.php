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
            Pending (<?= $run['nb_pending'] ?>)
        </a>
    </li>
    <li class="nav-item">
        <a
            class="nav-link text-primary <?= $this->isSelected($state) ? 'active' : '' ?>"
            href="<?= $this->url('runs.show', $run, ['state' => $this->selected()]) ?>"
        >
            Selected (<?= $run['nb_selected'] ?>)
        </a>
    </li>
    <li class="nav-item">
        <a
            class="nav-link text-danger <?= $this->isDiscarded($state) ? 'active' : '' ?>"
            href="<?= $this->url('runs.show', $run, ['state' => $this->discarded()]) ?>"
        >
            Discarded (<?= $run['nb_discarded'] ?>)
        </a>
    </li>
    <li class="nav-item">
        <a
            class="nav-link text-success <?= $this->isCurated($state) ? 'active' : '' ?>"
            href="<?= $this->url('runs.show', $run, ['state' => $this->curated()]) ?>"
        >
            Curated (<?= $run['nb_curated'] ?>)
        </a>
    </li>
</ul>

<?php $this->insert('runs/show/' . $state, ['run' => $run, 'publications' => $publications]) ?>
