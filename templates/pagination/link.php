<li class="page-item <?= $link['active'] ? 'active' : '' ?>">
    <a class="page-link" href="<?= $url(['page' => $link['page']]) ?>">
        <?= (string) $link['page'] ?>
    </a>
</li>
