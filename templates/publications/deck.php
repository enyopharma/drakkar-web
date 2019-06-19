<?php foreach($publications as $publication): ?>
<div class="row">
    <div class="col">
        <?php $this->insert('publications/card', ['publication' => $publication]) ?>
    </div>
</div>
<hr>
<?php endforeach; ?>
