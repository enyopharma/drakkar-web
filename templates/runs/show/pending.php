<h2>
    Pending publications:
</h2>

<?php if ($publications->count() == 0): ?>
<p>
    There is no pending publication.
</p>
<?php else: ?>
<?php $this->insert('publications/card', ['run' => $run, 'publication' => $publications->first()]) ?>
<?php endif ?>
