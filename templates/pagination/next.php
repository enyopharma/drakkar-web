<li class="page-item <?= $page == $total ? 'disabled': '' ?>">
    <a class="page-link" href="<?= $url(['page' => $page + 1]) ?>">
        next
    </a>
</li>
