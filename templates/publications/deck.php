<?php foreach($publications as $publication): ?>
<?php $this->insert('publications/card', ['run' => $run, 'publication' => $publication]) ?>
<?php endforeach; ?>
