<?php $metadata = $this->metadata($publication['metadata']); ?>
<div id="publication-<?= $publication['pmid'] ?>" class="card">
    <h3 class="card-header">
        Ref:
        <a href="<?= $this->url('runs.publications.index', $run) ?>">
            <?= $run['type'] ?> - <?= $run['name'] ?></a>
        /
        <a href="<?= $this->url('runs.publications.descriptions.index', $publication) ?>"><?= $publication['pmid'] ?></a>
        <span class="float-right badge <?= $this->badgeclass($publication['state']) ?>">
            <?= $publication['state'] ?>
        </span>
    </h3>
    <div class="card-body">
        <h4 class="card-title">
            <?php if ($metadata['journal'] != ''): ?>
            <strong>[<?= $metadata['journal'] ?>]</strong>
            <?php endif; ?>
            <?php if ($metadata['title'] != ''): ?>
            <a href="<?= $this->url('runs.publications.descriptions.index', $publication) ?>">
                <?= $this->highlight($metadata['title']) ?></a>
            <?php endif; ?>
        </h4>
        <?php foreach ($metadata['abstract'] as $abstract): ?>
        <p class="card-text">
            <?= $this->highlight($abstract) ?>
        </p>
        <?php endforeach; ?>
        <p class="card-text text-muted">
            &mdash;&nbsp;<?= implode(', ', $metadata['authors']) ?>
        </p>
        <div class="row">
            <div class="col-2 offset-8">
                <a
                    class="btn btn-sm btn-block btn-link"
                    style="text-decoration: none"
                    href="https://www.ncbi.nlm.nih.gov/pubmed/<?= $publication['pmid'] ?>"
                    target="_blank"
                >
                    PUBMED
                </a>
            </div>
            <div class="col-2">
                <form method="POST" action="https://sci-hub.tw" target="_blank" class="m-0 p-0">
                    <input type="hidden" name="request" value="<?= $publication['pmid'] ?>">
                    <button type="submit" class="btn btn-sm btn-block btn-link" style="text-decoration: none">
                        SCIHUB
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
            <div class="row">
                <div class="col">
                    <input type="hidden" name="_method" value="PUT" />
                    <input type="hidden" name="_source" value="<?= $source ?>" />
                    <textarea
                        class="form-control form-control-sm"
                        name="annotation"
                        rows="1"
                    ><?= $publication['annotation'] ?></textarea>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <button
                        type="submit"
                        name="state"
                        value="selected"
                        class="btn btn-sm btn-block btn-primary"
                    >
                        Select
                    </button>
                </div>
                <div class="col">
                    <button
                        type="submit"
                        name="state"
                        value="discarded"
                        class="btn btn-sm btn-block btn-danger"
                    >
                        Discard
                    </button>
                </div>
                <div class="col">
                    <button
                        type="submit"
                        name="state"
                        value="curated"
                        class="btn btn-sm btn-block btn-success"
                    >
                        Curated
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
