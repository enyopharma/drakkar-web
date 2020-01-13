<form method="GET" action="<?= $this->url('publications.index') ?>">
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
        <div class="col-2">
            <button type="submit" class="btn btn-block btn-primary">
                Search publication
            </button>
        </div>
    </div>
</form>
