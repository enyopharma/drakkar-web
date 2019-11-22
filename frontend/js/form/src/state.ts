import { Description, Method, Protein, Feedback, Alignment } from './types'

export type AppState = {
    ui: UI,
    description: Description,
}

export type UI = {
    query: string,
    method: Method | null,
    interactor1: InteractorUI,
    interactor2: InteractorUI,
    saving: boolean,
    feedback: Feedback | null
}

export type InteractorUI = {
    query: string,
    protein: Protein | null,
    editing: boolean,
    processing: boolean,
    alignment: Alignment | null,
}

export const init: AppState = {
    ui: {
        query: '',
        method: null,
        interactor1: {
            query: '',
            protein: null,
            editing: false,
            processing: false,
            alignment: null,
        },
        interactor2: {
            query: '',
            protein: null,
            editing: false,
            processing: false,
            alignment: null,
        },
        saving: false,
        feedback: null,
    },
    description: {
        method: { psimi_id: null },
        interactor1: {
            protein: { accession: null },
            name: '',
            start: null,
            stop: null,
            mapping: [],
        },
        interactor2: {
            protein: { accession: null },
            name: '',
            start: null,
            stop: null,
            mapping: [],
        },
    },
}
