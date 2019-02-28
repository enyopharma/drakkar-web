<!DOCTYPE html>
<html>
    <head>
        <title>
            <?= isset($title) ? $this->e($title) : 'Welcome' ?>
        </title>
        <link rel="stylesheet" href="<?= $this->asset('build/app.css') ?>" />
        <script type="text/javascript" src="<?= $this->asset('build/app.js') ?>"></script>
    </head>
    <body>
        <?= $this->section('content') ?>
    </body>
</html>
