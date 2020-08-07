import fetch from 'cross-fetch'

import { Protein } from './types'

export const proteins = {
    select: (id: number): Promise<Protein> => {
        return fetch(`/proteins/${id}`)
            .then(response => response.json(), error => console.log(error))
            .then(json => json.data, error => console.log(error))
    }
}

export const descriptions = {
    delete: (run_id: number, pmid: number, id: number): Promise<{ success: boolean }> => {
        return fetch(`/runs/${run_id}/publications/${pmid}/descriptions/${id}`, {
            method: 'DELETE',
            headers: {
                'accept': 'application/json',
                'content-type': 'application/json',
            },
        })
            .then(response => response.json(), error => console.log(error))
    }
}
