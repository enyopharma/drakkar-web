<div class="card">
    <h3 class="card-header">
        PMID: <?= $publication['pmid'] ?>
        <span class="float-right badge <?= $this->badgeclass($publication['state']) ?>">
            <?= $publication['state'] ?>
        </span>
    </h3>
    <div class="card-body">
        <h4 class="card-title">
            <a href="#"><?= $publication['title'] ?></a>
        </h4>
        <?php foreach ($publication['abstract'] as $abstract): ?>
        <p class="card-text">
            <?= nl2br($this->highlighted($abstract, $publication['run']['type'], $publication['patterns'])) ?>
        </p>
        <?php endforeach; ?>
        <p class="card-text text-muted">
            &mdash;&nbsp;<?= implode(', ', $publication['authors']) ?> in <?= $publication['journal'] ?>
        </p>
        <div class="row mt-0 mb-0">
            <div class="col-2 offset-8">
                <a
                    class="btn btn-sm btn-block btn-link"
                    style="text-decoration: none"
                    href="https://www.ncbi.nlm.nih.gov/pubmed/<?= $publication['pmid'] ?>"
                    target="_blank"
                >
                    PUBMED <i class="fas fa-external-link-alt"></i>
                </a>
            </div>
            <div class="col-2">
                <form method="POST" action="https://sci-hub.tw" target="_blank" class="m-0 p-0">
                    <input type="hidden" name="request" value="<?= $publication['pmid'] ?>">
                    <button type="submit" class="btn btn-sm btn-block btn-link" style="text-decoration: none">
                        SCIHUB <i class="fas fa-external-link-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <form
            method="POST"
            action="<?= $this->url('runs.publications', $publication) ?>"
        >
            <input type="hidden" name="_method" value="PUT" />
            <div class="row mt-0">
                <div class="col">
                    <textarea
                        class="form-control form-control-sm"
                        name="annotation"
                        rows="1"
                    ><?= $publication['annotation'] ?></textarea>
                </div>
            </div>
            <div class="row mb-0">
                <div class="col">
                    <button
                        type="submit"
                        name="state"
                        value="<?= $selected ?>"
                        class="btn btn-sm btn-block btn-primary"
                        <?= $publication['selected'] ? 'disabled' : '' ?>
                    >
                        Select
                    </button>
                </div>
                <div class="col">
                    <button
                        type="submit"
                        name="state"
                        value="<?= $discarded ?>"
                        class="btn btn-sm btn-block btn-danger"
                        <?= $publication['discarded'] ? 'disabled' : '' ?>
                    >
                        Discard
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
