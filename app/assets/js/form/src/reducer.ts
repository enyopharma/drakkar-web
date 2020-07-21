import { Reducer } from 'redux'

import { AppState, InteractorUI, InteractorI, Alignment, Feedback } from './types'
import { AppAction, AppActionTypes, InteractorAction, isInteractorAction } from './actions'

import { description } from './description'

export const initialState: AppState = {
    interactor1: {
        editing: false,
        processing: false,
        alignment: null,
    },
    interactor2: {
        editing: false,
        processing: false,
        alignment: null,
    },
    saving: false,
    feedback: null,
    description: {
        method: {
            psimi_id: null,
        },
        interactor1: {
            protein: {
                accession: null,
            },
            name: '',
            start: null,
            stop: null,
            mapping: [],
        },
        interactor2: {
            protein: {
                accession: null,
            },
            name: '',
            start: null,
            stop: null,
            mapping: [],
        },
    },
}

export const reducer: Reducer<AppState, AppAction> = (state: AppState = initialState, action: AppAction): AppState => {
    return {
        interactor1: interactor(1)(state.interactor1, action),
        interactor2: interactor(2)(state.interactor2, action),
        saving: saving(state.saving, action),
        feedback: feedback(state.feedback, action),
        description: description(state.description, action),
    }
}

const interactor = (i: InteractorI) => (state: InteractorUI, action: AppAction): InteractorUI => {
    if (isInteractorAction(action) && i == action.i) {
        return {
            editing: editing(state.editing, action),
            processing: processing(state.processing, action),
            alignment: alignment(state.alignment, action),
        }
    }

    return state
}

const editing = (state: boolean, action: InteractorAction): boolean => {
    switch (action.type) {
        case AppActionTypes.SELECT_PROTEIN:
            return action.protein.type == 'v'
        case AppActionTypes.EDIT_MATURE:
            return true
        case AppActionTypes.UPDATE_MATURE:
            return false
        case AppActionTypes.RESET_INTERACTOR:
            return false
        default:
            return state
    }
}

const processing = (state: boolean, action: InteractorAction): boolean => {
    switch (action.type) {
        case AppActionTypes.FIRE_ALIGNMENT:
            return true
        case AppActionTypes.ADD_ALIGNMENT:
            return false
        case AppActionTypes.CANCEL_ALIGNMENT:
            return false
        case AppActionTypes.RESET_INTERACTOR:
            return false
        default:
            return state
    }
}

const alignment = (state: Alignment | null, action: InteractorAction): Alignment | null => {
    switch (action.type) {
        case AppActionTypes.SHOW_ALIGNMENT:
            return action.alignment
        case AppActionTypes.ADD_ALIGNMENT:
            return null
        case AppActionTypes.CANCEL_ALIGNMENT:
            return null
        case AppActionTypes.RESET_INTERACTOR:
            return null
        default:
            return state
    }
}

const saving = (state: boolean, action: AppAction): boolean => {
    switch (action.type) {
        case AppActionTypes.FIRE_SAVE:
            return true
        case AppActionTypes.SHOW_FEEDBACK:
            return false
        default:
            return state
    }
}

const feedback = (_: any, action: AppAction): Feedback | null => {
    switch (action.type) {
        case AppActionTypes.SHOW_FEEDBACK:
            return { success: action.success, errors: action.errors }
        default:
            return null
    }
}
