import qs from 'query-string'
import fetch from 'cross-fetch'

const uuid = require('uuid/v4')

const method = {
    search: q => fetch('/methods?' + qs.stringify({q: q}))
        .then(response => response.json())
        .then(json => json.data.methods)
}

const protein = {
    search: (type, q) => fetch('/proteins?' + qs.stringify({type: type, q: q}))
        .then(response => response.json(), error => console.log(error))
        .then(json => json.data.proteins),

    select: accession => fetch('/proteins/' + accession)
        .then(response => response.json(), error => console.log(error))
        .then(json => json.data.protein),
}

const alignment = (query, sequences, handler) => {
    const id = uuid()

    const socket = new WebSocket(`ws://${window.location.host}:3000`, 'app')

    socket.onopen = () => socket.send(JSON.stringify({
        channel: 'echo',
        payload: {
            id: id,
            message: `Listening for job with id '${id}'.`,
        },
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
            handler(payload.alignment)
            socket.close()
        }
    }

    const request = fetch('/jobs/alignments', {
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
    })

    request.then(response => {}, error => console.log(error))
}

const save = (run_id, pmid, description, handler) => {
    const url = `/runs/${run_id}/publications/${pmid}/descriptions`

    const request = fetch(url, {
        method: 'POST',
        headers: {
            'accept': 'application/json',
            'content-type': 'application/json',
        },
        body: JSON.stringify(description)
    })

    request
        .then(response => response.json(), response => response.json())
        .then(json => handler(json))

}

export default { method, protein, alignment, save }
