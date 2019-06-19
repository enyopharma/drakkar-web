<?php foreach($publications as $publication): ?>
<div class="row">
    <div class="col">
        <?php $this->insert('publications/card', [
            'publication' => $publication,
            'redirect' => $this->url('runs.show', $publication['run'], ['state' => $publication['state']])
        ]) ?>
    </div>
</div>
<?php endforeach; ?>
