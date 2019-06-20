<?php foreach ($descriptions as $description): ?>
<div id="description-<?= $description['id'] ?>" class="row">
    <div class="col">
        <?= $this->insert('descriptions/card', ['description' => $description, 'redirect' => $redirect]) ?>
    </div>
</div>
<?php endforeach ?>
