<div class="row">
<div class="col">
<div class="card">
    <h4 class="card-header">
        <a
            class="<?= $this->textclass($publication['state']) ?>"
            data-toggle="collapse"
            href="#pmid-<?= $publication['pmid'] ?>"
        >
            <?= $publication['title'] ?>
        </a>
    </h4>
    <div id="pmid-<?= $publication['pmid'] ?>" class="collapse">
        <div class="card-body">
            <blockquote class="blockquote mb-0">
                <?php foreach ($publication['abstract'] as $abstract): ?>
                <p>
                    <?= nl2br($this->highlighted($abstract, $publication['run']['type'], $publication['patterns'])) ?>
                </p>
                <?php endforeach; ?>
                <footer class="blockquote-footer">
                    <?= implode(', ', $publication['authors']) ?>
                </footer>
            </blockquote>
            <div class="row">
                <div class="col offset-lg-8">
                    <form method="GET" action="https://www.ncbi.nlm.nih.gov/pubmed/<?= $publication['pmid'] ?>" target="_blank">
                        <input type="hidden" name="request" value="<?= $publication['pmid'] ?>">
                        <button type="submit" class="btn btn-block btn-info">
                            PUBMED <i class="fas fa-external-link-alt"></i>
                        </button>
                    </form>
                </div>
                <div class="col">
                    <form method="POST" action="https://sci-hub.tw" target="_blank">
                        <input type="hidden" name="request" value="<?= $publication['pmid'] ?>">
                        <button type="submit" class="btn btn-block btn-info">
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
                <div class="form-row">
                    <div class="col">
                        <textarea
                            class="form-control"
                            name="annotation"
                            rows="3"
                        ><?= $publication['annotation'] ?></textarea>
                    </div>
                </div>
                <hr>
                <div class="form-row">
                    <div class="col">
                        <button
                            type="submit"
                            name="state"
                            value="<?= $selected ?>"
                            class="btn btn-block btn-primary"
                            <?= $publication['selected'] ? 'disabled' : '' ?>
                        >
                            Selected
                        </button>
                    </div>
                    <div class="col">
                        <button
                            type="submit"
                            name="state"
                            value="<?= $discarded ?>"
                            class="btn btn-block btn-danger"
                            <?= $publication['discarded'] ? 'disabled' : '' ?>
                        >
                            Discarded
                        </button>
                    </div>
                    <div class="col">
                        <button
                            type="submit"
                            name="state"
                            value="<?= $curated ?>"
                            class="btn btn-block btn-success"
                            <?= $publication['curated'] ? '' : 'disabled' ?>
                        >
                            Curated
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>
