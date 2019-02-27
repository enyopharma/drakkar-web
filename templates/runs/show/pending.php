<h2 class="my-4">
    Pending publications:
</h2>

<?php if ($publications->count() == 0): ?>
<p>
    There is no pending publication.
</p>
<?php else: ?>
<?php $this->insert('publications/list', ['run' => $run, 'publications' => $publications]) ?>
<?php endif ?>
