<h2 class="my-4">
    Discarded publications:
</h2>

<?php if ($publications->count() == 0): ?>
<p>
    There is no discarded publication.
</p>
<?php else: ?>
<?php $this->insert('publications/deck', ['run' => $run, 'publications' => $publications]) ?>
<?php endif ?>
