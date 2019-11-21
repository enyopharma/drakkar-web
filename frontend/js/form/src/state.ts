import { Description, Feedback, Alignment } from './types'

export type AppState = {
    description: Description,
    ui: UI,
}

export type UI = {
    method: {
        query: string,
    }
    interactor1: InteractorUI,
    interactor2: InteractorUI,
    saving: boolean,
    feedback: Feedback
}

export type InteractorUI = {
    protein: {
        query: string,
    }
    editing: boolean,
    processing: boolean,
    alignment: Alignment,
}
