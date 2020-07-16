<form method="GET" action="<?= $this->url('descriptions.index') ?>">
    <div class="row">
        <div class="col">
            <input
                type="text"
                class="stable_id form-control"
                name="stable_id"
                value="<?= $stable_id ?? '' ?>"
                placeholder="Stable id"
            />
        </div>
        <div class="col-4">
            <button type="submit" class="btn btn-block btn-primary">
                Search description
            </button>
        </div>
    </div>
</form>
