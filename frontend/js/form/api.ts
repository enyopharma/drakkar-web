import qs from 'query-string'
import fetch from 'cross-fetch'

import { Description, Method, ProteinType, Protein } from './types'
import { Sequences, Alignment } from './types'
import { SearchResult, Feedback } from './types'

const uuid = require('uuid/v4')

export const methods = {
    search: async (q: string): Promise<SearchResult[]> => {
        return fetch('/methods?' + qs.stringify({ q: q }))
            .then(response => response.json(), error => console.log(error))
            .then(json => json.data.map(method => ({
                value: method.psimi_id, label: [
                    method.psimi_id,
                    method.name,
                ].join(' - '),
            })))
    },

    select: async (psimi_id: string): Promise<Method> => {
        return fetch(`/methods/${psimi_id}`)
            .then(response => response.json(), error => console.log(error))
            .then(json => json.data)
    }
}

export const proteins = {
    search: async (type: ProteinType, q: string): Promise<SearchResult[]> => {
        return fetch('/proteins?' + qs.stringify({ type: type, q: q }))
            .then(response => response.json(), error => console.log(error))
            .then(json => json.data.map(protein => ({
                value: protein.accession, label: [
                    protein.accession,
                    protein.taxon,
                    protein.name,
                    protein.description,
                ].join(' - '),
            })))
    },

    select: async (accession: string): Promise<Protein> => {
        return fetch(`/proteins/${accession}`)
            .then(response => response.json(), error => console.log(error))
            .then(json => json.data)
    }
}

export const alignment = async (query: string, sequences: Sequences): Promise<Alignment> => {
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

        const params = {
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
        }

        fetch('/jobs/alignments', params).catch(error => reject(error))
    })
}

export const save = async (run_id: number, pmid: number, description: Description): Promise<Feedback> => {
    const params = {
        method: 'POST',
        headers: {
            'accept': 'application/json',
            'content-type': 'application/json',
        },
        body: JSON.stringify(description)
    }

    return fetch(`/runs/${run_id}/publications/${pmid}/descriptions`, params)
        .then(response => response.json(), error => console.log(error))
        .then(json => ({ success: json.success, errors: json.reason ? [json.reason] : json.errors }))
}
