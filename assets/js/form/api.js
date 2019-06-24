import qs from 'query-string'
import fetch from 'cross-fetch'

const uuid = require('uuid/v4')

const methods = {
    search: q => fetch('/methods?' + qs.stringify({q: q}))
        .then(response => response.json())
        .then(json => json.data.methods, error => console.log(error)),

    select: psimi_id => fetch(`/methods/${psimi_id}`)
        .then(response => response.json(), error => console.log(error))
        .then(json => json.data.method, error => console.log(error)),
}

const proteins = {
    search: (type, q) => fetch('/proteins?' + qs.stringify({type: type, q: q}))
        .then(response => response.json(), error => console.log(error))
        .then(json => json.data.proteins, error => console.log(error)),

    select: accession => fetch(`/proteins/${accession}`)
        .then(response => response.json(), error => console.log(error))
        .then(json => json.data.protein, error => console.log(error)),
}

const alignment = (query, sequences) => {
    const id = uuid()

    return new Promise((resolve, reject) => {
        const socket = new WebSocket(`ws://${window.location.host}:3000`, 'app')

        socket.onopen = () => socket.send(JSON.stringify({
            channel: 'echo',
            payload: { id: id, message: `Listening for job with id '${id}'.` },
        }))

        socket.onmessage = event => {
            const message = JSON.parse(event.data)

            const channel = message.channel;
            const payload = message.payload;

            if (channel == 'echo' && payload.id == id) {
                console.log(payload.message)
                return;
            }

            if (channel == 'alignment' && payload.id == id) {
                socket.close()
                resolve(payload.alignment)
            }
        }

        fetch('/jobs/alignments', {
            method: 'POST',
            headers: {
                'accept': 'application/json',
                'content-type': 'application/json',
            },
            body: JSON.stringify({
                id: id,
                query: query,
                sequences: sequences,
            })
        }).catch(error => reject(error))
    })
}

const save = (run_id, pmid, body) => {
    return fetch(`/runs/${run_id}/publications/${pmid}/descriptions`, {
        method: 'POST',
        headers: {
            'accept': 'application/json',
            'content-type': 'application/json',
        },
        body: JSON.stringify(body)
    })
    .then(response => response.json(), error => console.log(error))
    .then(json => json, error => console.log(error))
}

export default { methods, proteins, alignment, save }
