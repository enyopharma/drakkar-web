<h2>
    Selected publications:
</h2>

<?php if ($publications->count() == 0): ?>
<p>
    There is no selected publication.
</p>
<?php else: ?>
<?php $this->insert('publications/list', ['run' => $run, 'publications' => $publications]) ?>
<?php endif ?>