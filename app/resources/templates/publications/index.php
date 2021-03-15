<?php
    use App\Assertions\PublicationState;

    $header = match ($state) {
        PublicationState::PENDING => 'Pending publications',
        PublicationState::SELECTED => 'Selected publications',
        PublicationState::DISCARDED => 'Discarded publications',
        PublicationState::CURATED => 'Curated publications',
    };

    $empty = match ($state) {
        PublicationState::PENDING => 'There is no pending publication.',
        PublicationState::SELECTED => 'There is no selected publication.',
        PublicationState::DISCARDED => 'There is no discarded publication.',
        PublicationState::CURATED => 'There is no curated publication.',
    };

    $tabs = [
        [
            'classes' => $state == PublicationState::PENDING
                ? 'nav-link text-warning active'
                : 'nav-link text-warning',
            'url' => $this->url('runs.publications.index', $run, ['state' => PublicationState::PENDING], 'publications'),
            'name' => sprintf('Pending (%s)', $run['nbs'][PublicationState::PENDING]),
        ],
        [
            'classes' => $state == PublicationState::SELECTED
                ? 'nav-link text-primary active'
                : 'nav-link text-primary',
            'url' => $this->url('runs.publications.index', $run, ['state' => PublicationState::SELECTED], 'publications'),
            'name' => sprintf('Selected (%s)', $run['nbs'][PublicationState::SELECTED]),
        ],
        [
            'classes' => $state == PublicationState::DISCARDED
                ? 'nav-link text-danger active'
                : 'nav-link text-danger',
            'url' => $this->url('runs.publications.index', $run, ['state' => PublicationState::DISCARDED], 'publications'),
            'name' => sprintf('Discarded (%s)', $run['nbs'][PublicationState::DISCARDED]),
        ],
        [
            'classes' => $state == PublicationState::CURATED
                ? 'nav-link text-success active'
                : 'nav-link text-success',
            'url' => $this->url('runs.publications.index', $run, ['state' => PublicationState::CURATED], 'publications'),
            'name' => sprintf('Curated (%s)', $run['nbs'][PublicationState::CURATED]),
        ],
    ];

    $pagination = $this->pagination($total, $page, $limit, fn (int $page) => $this->url(
        'runs.publications.index',
        $run,
        ['state' => $state, 'page' => $page, 'limit' => $limit],
        'publications',
    ));
?>

<?php $this->layout('layout'); ?>

<div class="page-header">
    <h1>
        <a href="<?= $this->url('runs.index') ?>">Drakkar</a> &gt;
        Run <?= $run['name'] ?>
    </h1>
</div>

<h2 id="publications">
    <?= $header ?>
</h2>

<nav>
    <ul class="nav nav-tabs nav-fill">
        <?php foreach ($tabs as $tab): ?>
        <li class="nav-item">
            <a class="<?= $tab['classes'] ?>" href="<?= $tab['url'] ?>">
                <?= $this->e($tab['name']) ?>
            </a>
        </li>
        <?php endforeach ?>
    </ul>
</nav>

<?php if (count($publications) == 0): ?>
<p>
    <?= $empty ?>
</p>
<?php else: ?>
<?= $pagination ?>
<?php foreach($publications as $publication): ?>
<?php $this->insert('publications/card', [
    'run' => $run,
    'publication' => $publication,
    'source' => $this->url('runs.publications.index', $run,
        ['state' => $state, 'page' => $page, 'limit' => $limit],
        'publications',
    ),
]) ?>
<?php endforeach ?>
<?= $pagination ?>
<?php endif ?>
