<!DOCTYPE html>
<html>
    <head>
        <title>
            <?= isset($title) ? $this->e($title) : 'Welcome' ?>
        </title>
        <link rel="stylesheet" href="<?= $this->asset('app.css') ?>" />
        <?= $this->section('styles') ?>
        <script type="text/javascript" src="<?= $this->asset('runtime.js') ?>"></script>
        <script type="text/javascript" src="<?= $this->asset('app.js') ?>"></script>
        <?= $this->section('scripts') ?>
    </head>
    <body>
        <?= $this->section('content') ?>
    </body>
</html>
