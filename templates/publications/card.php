<div class="card">
    <h4 class="card-header">
        <a
            class="<?= $this->stateMap($publication['state'])['styles']['text'] ?>"
            data-toggle="collapse"
            href="#pmid-<?= $publication['pmid'] ?>"
        >
            <?= $publication['title'] ?>
        </a>
    </h4>
    <div id="pmid-<?= $publication['pmid'] ?>" class="collapse">
        <div class="card-body">
            <blockquote class="blockquote mb-0">
                <p>
                    <?= nl2br($this->highlighted($publication['type'], $publication['abstract'])) ?>
                </p>
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
                action="<?= $this->url('runs.publications.update', $publication) ?>"
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
                            value="<?= $this->selected() ?>"
                            class="btn btn-block btn-primary"
                            <?= $this->isSelected($publication['state']) ? 'disabled' : '' ?>
                        >
                            Selected
                        </button>
                    </div>
                    <div class="col">
                        <button
                            type="submit"
                            name="state"
                            value="<?= $this->discarded() ?>"
                            class="btn btn-block btn-danger"
                            <?= $this->isDiscarded($publication['state']) ? 'disabled' : '' ?>
                        >
                            Discarded
                        </button>
                    </div>
                    <div class="col">
                        <button
                            type="submit"
                            name="state"
                            value="<?= $this->curated() ?>"
                            class="btn btn-block btn-success"
                            <?= $this->isSelected($publication['state']) ? '' : 'disabled' ?>
                        >
                            Curated
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
