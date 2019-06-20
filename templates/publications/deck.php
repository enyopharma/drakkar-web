<?php foreach($publications as $publication): ?>
<div class="row">
    <div class="col">
        <?php $this->insert('publications/card', [
            'publication' => $publication,
            'redirect' => isset($redirect) ? $redirect : '',
        ]) ?>
    </div>
</div>
<?php endforeach; ?>
