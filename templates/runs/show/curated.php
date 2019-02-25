<h2>
    Curated publications:
</h2>

<?php if ($publications->count() == 0): ?>
<p>
    There is no curated publication.
</p>
<?php else: ?>
<?php $this->insert('publications/list', ['run' => $run, 'publications' => $publications]) ?>
<?php endif ?>
