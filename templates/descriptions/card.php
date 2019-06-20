<div class="card description">
    <div class="card-body">
        <div class="row mt-0">
            <div class="col">
                <strong>
                    <?= $description['method']['psimi_id'] ?>
                </strong>
                -
                <?= $description['method']['name'] ?>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <strong><?= $description['interactor1']['protein']['accession'] ?></strong>/<?= $description['interactor1']['name'] ?>
            </div>
            <div class="col">
                <strong><?= $description['interactor2']['protein']['accession'] ?></strong>/<?= $description['interactor2']['name'] ?>
                <?php if ($description['type'] == 'vh'): ?>
                (<?= $description['interactor2']['start'] ?>,
                    <?= $description['interactor2']['stop'] ?>)
                <?php endif ?>
            </div>
        </div>
        <div class="row mb-0">
            <div class="col mapping">
                <?= $this->insert('mapping/deck', [
                    'type' => $description['interactor1']['type'],
                    'mapping' => $description['interactor1']['mapping'],
                ]) ?>
            </div>
            <div class="col mapping">
                <?= $this->insert('mapping/deck', [
                    'type' => $description['interactor2']['type'],
                    'mapping' => $description['interactor2']['mapping'],
                ]) ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="row my-0">
            <div class="col offset-8">
                <a
                    class="btn btn-sm btn-block btn-primary"
                    href="<?= $this->url('runs.publications.descriptions.edit', $description) ?>"
                    target="_blank"
                >
                    <i class="fas fa-copy"></i> Copy
                </a>
            </div>
            <div class="col">
                <a
                    class="btn btn-sm btn-block btn-danger <?= $description['deleted'] ? 'disabled' : '' ?>"
                    href="#"
                >
                    <i class="fas fa-trash"></i> Delete
                </a>
            </div>
        </div>
    </div>
</div>
