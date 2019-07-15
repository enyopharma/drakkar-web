<?php foreach($publications as $publication): ?>
<?php $this->insert('publications/card', ['publication' => $publication, 'redirect' => $redirect]) ?>
<?php endforeach; ?>
