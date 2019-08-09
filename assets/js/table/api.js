import fetch from 'cross-fetch'

const proteins = {
    select: accession => fetch(`/proteins/${accession}`)
        .then(response => response.json(), error => console.log(error))
        .then(json => json.data, error => console.log(error)),
}

const descriptions = {
    delete: (run_id, pmid, id) => fetch(`/runs/${run_id}/publications/${pmid}/descriptions/${id}`, {
        method: 'DELETE',
        headers: {
            'accept': 'application/json',
            'content-type': 'application/json',
        },
    })
    .then(response => response.json(), error => console.log(error))
    .then(json => json, error => console.log(error)),
}

export default { proteins, descriptions }
