import qs from 'query-string'
import fetch from 'cross-fetch'

import { Description } from './types'
import { MethodSearchResult, Method } from './types'
import { ProteinType, ProteinSearchResult, Protein } from './types'
import { Sequences, Alignment } from './types'
import { Feedback } from './types'

const uuid = require('uuid/v4')

export const methods = {
    search: async (q: string): Promise<MethodSearchResult[]> => fetch('/methods?' + qs.stringify({ q: q }))
        .then(response => response.json())
        .then(json => json.data, error => console.log(error)),

    select: async (psimi_id: string): Promise<Method> => fetch(`/methods/${psimi_id}`)
        .then(response => response.json(), error => console.log(error))
        .then(json => json.data, error => console.log(error)),
}

export const proteins = {
    search: async (type: ProteinType, q: string): Promise<ProteinSearchResult[]> => fetch('/proteins?' + qs.stringify({ type: type, q: q }))
        .then(response => response.json(), error => console.log(error))
        .then(json => json.data, error => console.log(error)),

    select: async (accession: string): Promise<Protein> => fetch(`/proteins/${accession}`)
        .then(response => response.json(), error => console.log(error))
        .then(json => json.data, error => console.log(error)),
}

export const alignment = (query: string, sequences: Sequences): Promise<Alignment> => {
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

export const save = async (run_id: number, pmid: number, description: Description): Promise<Feedback> => {
    return fetch(`/runs/${run_id}/publications/${pmid}/descriptions`, {
        method: 'POST',
        headers: {
            'accept': 'application/json',
            'content-type': 'application/json',
        },
        body: JSON.stringify(description)
    })
        .then(response => response.json(), error => console.log(error))
        .then(json => ({ success: json.success, errors: json.reason ? [json.reason] : json.errors }))
}
