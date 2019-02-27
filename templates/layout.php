<!DOCTYPE html>
<html>
    <head>
        <title>
            <?= isset($title) ? $this->e($title) : 'Drakkar curation interface' ?>
        </title>
        <link rel="stylesheet" href="<?= $this->asset('build/app.css') ?>" />
        <script type="text/javascript" src="<?= $this->asset('build/app.js') ?>"></script>
    </head>
    <body>
        <div class="container">
            <?= $this->section('content') ?>
        </div>
    </body>
</html>
