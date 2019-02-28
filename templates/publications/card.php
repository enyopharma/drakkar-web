<div class="card">
    <h4 class="card-header">
        <a
            class="<?= $this->textStyles($publication['state']) ?>"
            data-toggle="collapse"
            href="#pmid-<?= $publication['pmid'] ?>"
        >
            <?= $publication['title'] == '' ? $publication['pmid'] : $publication['title'] ?>
        </a>
    </h4>
    <div id="pmid-<?= $publication['pmid'] ?>" class="collapse">
        <div class="card-body">
            <blockquote class="blockquote mb-0">
                <p>
                    <?= $publication['abstract'] ?>
                </p>
                <p>
                    <?= $publication['journal'] ?>
                </p>
                <footer class="blockquote-footer">
                    <?= $publication['authors'] ?>
                </footer>
            </blockquote>
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
