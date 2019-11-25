import { Description, Method, Protein, Feedback, Alignment } from './types'

export type AppState = {
    ui: UI,
    description: Description,
}

export type UI = {
    init: boolean,
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
