import { Description, Method, Protein, Feedback, Alignment } from './types'

export type AppState = {
    ui: UI,
    description: Description,
}

export type UI = {
    query: string,
    method: Method,
    interactor1: InteractorUI,
    interactor2: InteractorUI,
    saving: boolean,
    feedback: Feedback
}

export type InteractorUI = {
    query: string,
    protein: Protein,
    editing: boolean,
    processing: boolean,
    alignment: Alignment,
}
