<?php foreach($mapping as $alignment): ?>
<div class="row">
    <div class="col">
        <?= $this->insert('mapping/card', ['type' => $type, 'alignment' => $alignment]) ?>
    </div>
</div>
<?php endforeach ?>
