<?php $this->layout('layout'); ?>

<?php if (count($descriptions) > 0): ?>
<?php $this->push('scripts'); ?>
<script type="text/javascript" src="<?= $this->asset('table.js') ?>"></script>
<script type="text/javascript">
    descriptions.table('descriptions-table', <?= json_encode($descriptions) ?>);
</script>
<?php $this->end(); ?>
<?php endif; ?>

<div class="page-header">
    <h1>
        <a href="<?= $this->url('runs.index') ?>">
            Drakkar</a>
        &gt;
        <a href="<?= $this->url('runs.publications.index', $run['url']) ?>">
            <?= $run['type'] ?> - <?= $run['name'] ?></a>
        &gt;
        <?= $publication['pmid'] ?>
    </h1>
</div>

<?= $this->insert('publications/card', [
    'publication' => $publication,
    'source' => $this->url('runs.publications.descriptions.index', $publication['url'])
]) ?>

<h2 id="descriptions">
    Descriptions associated with this publication
</h2>

<?php if ($publication['state'] == $selected): ?>
<p>
    <a href="<?= $this->url('runs.publications.descriptions.create', $publication['url']) ?>">
        Add new descriptions.
    </a>
</p>
<?php else: ?>
<p class="text-warning">
    Publication state must be 'selected' in order to add new descriptions.
</p>
<?php endif ?>

<?php if (count($descriptions) == 0): ?>
<p>
    There is no descriptions associated with this publication.
</p>
<?php else: ?>
<?php $this->insert('pagination/nav', [
    'pagination' => $this->pagination($total, $page, $limit),
    'url' => function (int $page) use ($publication, $limit) {
        return $this->url(
            'runs.publications.descriptions.index',
            $publication['url'],
            ['page' => $page, 'limit' => $limit],
            'descriptions'
        );
    },
]) ?>
<div class="row">
    <div class="col">
        <div id="descriptions-table"></div>
    </div>
</div>
<?php $this->insert('pagination/nav', [
    'pagination' => $this->pagination($total, $page, $limit),
    'url' => function (int $page) use ($publication, $limit) {
        return $this->url(
            'runs.publications.descriptions.index',
            $publication['url'],
            ['page' => $page, 'limit' => $limit],
            'descriptions'
        );
    },
]) ?>
<?php endif ?>
