<nav>
    <ul class="pagination justify-content-center">
        <?php $this->insert('pagination/prev', [
            'page' => $pagination->page(),
            'url' => $url,
        ]) ?>
        <?php $links = $pagination->links(); ?>
        <?php for ($i = 0; $i < count($links); $i++): ?>
        <?php foreach ($links[$i] as $link): ?>
        <?php $this->insert('pagination/link', ['link' => $link, 'url' => $url]) ?>
        <?php endforeach; ?>
        <?php if ($i < count($links) - 1): ?>
        <?php $this->insert('pagination/spacer') ?>
        <?php endif; ?>
        <?php endfor; ?>
        <?php $this->insert('pagination/next', [
            'page' => $pagination->page(),
            'total' => $pagination->total(),
            'url' => $url,
        ]) ?>
    </ul>
</nav>
