<!DOCTYPE html>
<html>
    <head>
        <title>
            <?= isset($title) ? $this->e($title) : 'Drakkar curation interface' ?>
        </title>
        <link rel="stylesheet" href="<?= $this->asset('build/app.css') ?>" />
    </head>
    <body>
        <div class="container">
            <?= $this->section('content') ?>
        </div>
    </body>
</html>
