import qs from 'query-string'
import fetch from 'cross-fetch'
const uuid = require('uuid/v4')

import { SearchResult, Description, Method, ProteinType, Protein, Sequences, Alignment, Feedback } from './src/types'

type Cache = Record<string, SearchResult[]>

const mcache: Cache = {}
const pcache: Record<ProteinType, Cache> = { h: {}, v: {} }

export const methods = {
    search: (limit: number) => {
        return (query: string): SearchResult[] => {
            if (query == '') return []
            if (mcache[query]) return mcache[query]

            throw new Promise((resolve) => {
                setTimeout(() => {
                    fetch('/methods?' + qs.stringify({ query: query, limit: limit }))
                        .then(response => response.json(), error => console.log(error))
                        .then(json => mcache[query] = json.data.map((m: Method) => ({
                            value: m.psimi_id,
                            label: [m.psimi_id, m.name].join(' - '),
                        })))
                        .finally(resolve)
                }, 300)
            })
        }
    },

    select: async (psimi_id: string): Promise<Method> => {
        return fetch(`/methods/${psimi_id}`)
            .then(response => response.json(), error => console.log(error))
            .then(json => json.data)
    }
}

export const proteins = {
    search: (type: ProteinType, limit: number) => {
        return (query: string): SearchResult[] => {
            if (query == '') return []
            if (pcache[type][query]) return pcache[type][query]

            throw new Promise((resolve) => {
                setTimeout(() => {
                    fetch('/proteins?' + qs.stringify({ type: type, query: query, limit: limit }))
                        .then(response => response.json(), error => console.log(error))
                        .then(json => pcache[type][query] = json.data.map((p: Protein) => ({
                            value: p.accession,
                            label: [p.accession, p.taxon, p.name, p.description].join(' - '),
                        })))
                        .finally(resolve)
                }, 300)
            })
        }
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
