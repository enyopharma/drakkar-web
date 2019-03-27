<?php foreach($publications as $publication): ?>
<?php $this->insert('publications/card', ['type' => $type, 'publication' => $publication]) ?>
<?php endforeach; ?>
