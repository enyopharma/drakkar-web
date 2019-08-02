const init = (container, run_id) => $(container).on('submit', e => {
    const pmid = $(e.target).find('.pmid').val().trim()

    if (pmid.length > 0) {
        window.open(`/runs/${run_id}/publications/${pmid}/descriptions`)
    }

    return false
});

window.search = { form: init }
