<!DOCTYPE html>
<html>
    <head>
        <title>
            <?= isset($title) ? $this->e($title) : 'Welcome' ?>
        </title>
        <link rel="stylesheet" href="<?= $this->asset('build/app.css') ?>" />
        <?= $this->section('styles') ?>
        <script type="text/javascript" src="<?= $this->asset('build/app.js') ?>"></script>
        <?= $this->section('scripts') ?>
    </head>
    <body>
        <?= $this->section('content') ?>
    </body>
</html>
