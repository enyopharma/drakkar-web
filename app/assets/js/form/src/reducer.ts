import { Reducer } from 'redux'

import { AppState } from './state'
import { AppAction } from './actions'

import { ui } from './reducers/ui'
import { description } from './reducers/description'

export const reducer: Reducer<AppState, AppAction> = (state: AppState = initialState, action: AppAction): AppState => {
    return {
        ui: ui(state.ui, action),
        description: description(state.description, action),
    }
}

export const initialState: AppState = {
    ui: {
        init: false,
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
