<?php foreach($publications as $publication): ?>
<?php $this->insert('publications/card', ['publication' => $publication, 'source' => $source]) ?>
<?php endforeach; ?>
