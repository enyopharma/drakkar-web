<h2>
    Discarded publications:
</h2>

<?php if ($publications->count() == 0): ?>
<p>
    There is no discarded publication.
</p>
<?php else: ?>
<?php $this->insert('publications/list', ['run' => $run, 'publications' => $publications]) ?>
<?php endif ?>