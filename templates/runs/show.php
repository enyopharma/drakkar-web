<?php
    use App\Repositories\Association;
?>
<?php $this->layout('layout'); ?>
<div class="page-header">
    <h1>
        <a href="<?= $this->url('index') ?>">Drakkar</a>
        &gt;
        Run <?= $run['name'] ?>
    </h1>
</div>

<ul class="nav nav-tabs nav-fill">
    <li class="nav-item">
        <a
            class="nav-link text-warning <?= $state == Association::PENDING ? 'active' : '' ?>"
            href="<?= $this->url('runs.show', $run, ['state' => Association::PENDING]) ?>"
        >
            Pending (<?= $run['nb_pending'] ?>)
        </a>
    </li>
    <li class="nav-item">
        <a
            class="nav-link text-primary <?= $state == Association::SELECTED ? 'active' : '' ?>"
            href="<?= $this->url('runs.show', $run, ['state' => Association::SELECTED]) ?>"
        >
            Selected (<?= $run['nb_selected'] ?>)
        </a>
    </li>
    <li class="nav-item">
        <a
            class="nav-link text-danger <?= $state == Association::DISCARDED ? 'active' : '' ?>"
            href="<?= $this->url('runs.show', $run, ['state' => Association::DISCARDED]) ?>"
        >
            Discarded (<?= $run['nb_discarded'] ?>)
        </a>
    </li>
    <li class="nav-item">
        <a
            class="nav-link text-success <?= $state == Association::CURATED ? 'active' : '' ?>"
            href="<?= $this->url('runs.show', $run, ['state' => Association::CURATED]) ?>"
        >
            Curated (<?= $run['nb_curated'] ?>)
        </a>
    </li>
</ul>
