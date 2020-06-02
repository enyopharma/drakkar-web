<nav>
    <ul class="pagination justify-content-center">
        <?php $this->insert('pagination/link', [
            'active' => $prev['active'],
            'enabled' => $prev['enabled'],
            'page' => $prev['page'],
            'url' => $url($prev['page']),
            'label' => 'prev',
        ]) ?>
        <?php for ($i = 0; $i < count($links); $i++): ?>
        <?php foreach ($links[$i] as $link): ?>
        <?php $this->insert('pagination/link', [
            'active' => $link['active'],
            'enabled' => $link['enabled'],
            'page' => $link['page'],
            'url' => $url($link['page']),
            'label' => (string) $link['page'],
        ]) ?>
        <?php endforeach; ?>
        <?php if ($i < count($links) - 1): ?>
        <?php $this->insert('pagination/spacer') ?>
        <?php endif; ?>
        <?php endfor; ?>
        <?php $this->insert('pagination/link', [
            'active' => $next['active'],
            'enabled' => $next['enabled'],
            'page' => $next['page'],
            'url' => $url($next['page']),
            'label' => 'next',
        ]) ?>
    </ul>
</nav>
