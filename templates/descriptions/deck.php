<?php foreach ($descriptions as $description): ?>
<div id="description-<?= $description['id'] ?>" class="row">
    <div class="col">
        <?= $this->insert('descriptions/card', ['description' => $description]) ?>
    </div>
</div>
<?php endforeach ?>
