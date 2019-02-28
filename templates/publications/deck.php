<?php foreach($publications as $publication): ?>
<?php $this->insert('publications/card', ['publication' => $publication]) ?>
<?php endforeach; ?>
