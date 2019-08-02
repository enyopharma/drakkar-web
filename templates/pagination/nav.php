<nav>
    <ul class="pagination justify-content-center">
        <?php $this->insert('pagination/link', [
            'active' => $pagination['prev']['active'],
            'enabled' => $pagination['prev']['enabled'],
            'page' => $pagination['prev']['page'],
            'url' => $url($pagination['prev']['page']),
            'label' => 'prev',
        ]) ?>
        <?php for ($i = 0; $i < count($pagination['links']); $i++): ?>
        <?php foreach ($pagination['links'][$i] as $link): ?>
        <?php $this->insert('pagination/link', [
            'active' => $link['active'],
            'enabled' => $link['enabled'],
            'page' => $link['page'],
            'url' => $url($link['page']),
            'label' => (string) $link['page'],
        ]) ?>
        <?php endforeach; ?>
        <?php if ($i < count($pagination['links']) - 1): ?>
        <?php $this->insert('pagination/spacer') ?>
        <?php endif; ?>
        <?php endfor; ?>
        <?php $this->insert('pagination/link', [
            'active' => $pagination['next']['active'],
            'enabled' => $pagination['next']['enabled'],
            'page' => $pagination['next']['page'],
            'url' => $url($pagination['next']['page']),
            'label' => 'next',
        ]) ?>
    </ul>
</nav>
