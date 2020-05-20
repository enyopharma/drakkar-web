<?php foreach($publications as $publication): ?>
<?php $this->insert('publications/card', ['run' => $run, 'publication' => $publication, 'source' => $source]) ?>
<?php endforeach; ?>
