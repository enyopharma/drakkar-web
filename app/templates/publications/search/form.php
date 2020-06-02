<form method="GET" action="<?= $url->generate('publications.index') ?>">
    <div class="row">
        <div class="col">
            <input
                type="text"
                class="pmid form-control"
                name="pmid"
                value="<?= isset($pmid) ? $pmid : '' ?>"
                placeholder="PMID"
            />
        </div>
        <div class="col-4">
            <button type="submit" class="btn btn-block btn-primary">
                Search publication
            </button>
        </div>
    </div>
</form>
