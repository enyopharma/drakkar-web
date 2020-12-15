import qs from 'query-string'
import fetch from 'cross-fetch'
import { v4 as uuid } from 'uuid'

import { cache } from './cache'
import { Resource, SearchResult, Description, Method, ProteinType, Protein, Sequences, Alignment, Feedback } from './types'

const cmethod = cache<Method>()
const cmethods = cache<SearchResult[]>()

const fetchMethod = async (id: number) => {
    return fetch(`/methods/${id}`)
        .then(response => response.json(), error => console.log(error))
        .then(json => json.data)
}

const fetchMethods = async (query: string, limit: number) => {
    return fetch('/methods?' + qs.stringify({ query: query, limit: limit }))
        .then(response => response.json(), error => console.log(error))
        .then(json => json.data.map((m: Method) => ({
            id: m.id,
            label: [m.psimi_id, m.name].join(' - '),
        })))
}

export const methods = {
    select: (id: number): Resource<Method> => {
        return cmethod.resource(id, () => fetchMethod(id), 10)
    },

    search: (query: string): Resource<SearchResult[]> => {
        return cmethods.resource(query, () => fetchMethods(query, 5), 300)
    },
}

const cprotein = cache<Protein>()
const cproteins = cache<SearchResult[]>()

const fetchProtein = async (id: number) => {
    return fetch(`/proteins/${id}`)
        .then(response => response.json(), error => console.log(error))
        .then(json => json.data)
}

const fetchProteins = async (type: ProteinType, query: string, limit: number) => {
    return fetch('/proteins?' + qs.stringify({ type: type, query: query, limit: limit }))
        .then(response => response.json(), error => console.log(error))
        .then(json => json.data.map((p: Protein) => ({
            id: p.id,
            label: [p.accession, p.version, p.taxon, p.name, p.description].join(' - '),
        })))
}

export const proteins = {
    select: (id: number): Resource<Protein> => {
        return cprotein.resource(id, () => fetchProtein(id), 10)
    },

    search: (type: ProteinType, query: string): Resource<SearchResult[]> => {
        return cproteins.resource(`${type}:${query}`, () => fetchProteins(type, query, 5), 300)
    },
}

export const alignment = async (query: string, sequences: Sequences): Promise<Alignment> => {
    const id = uuid()

    return new Promise((resolve, reject) => {
        const socket = new WebSocket(`ws://${window.location.host}/socket`, 'app')

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
        .then(json => ({ success: json.success, errors: json.reason ? json.reason : json.errors }))
}
