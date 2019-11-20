import { AppActionTypes, AppAction } from './actions'
import { InteractorAction, isInteractorAction } from './actions'

import { UInterface, InteractorInterface, InteractorI, Feedback, Alignment } from '../types'

export const uinterface = (state: UInterface, action: AppAction): UInterface => {
    return {
        method: {
            query: qmethod(state.method.query, action),
        },
        interactor1: interactor(1)(state.interactor1, action),
        interactor2: interactor(2)(state.interactor2, action),
        saving: saving(state.saving, action),
        feedback: feedback(state.feedback, action),
    }
}

const qmethod = (state: string, action: AppAction): string => {
    switch (action.type) {
        case AppActionTypes.UPDATE_METHOD_QUERY:
            return action.query
        case AppActionTypes.RESET_FORM:
            return ''
        default:
            return state
    }
}

const interactor = (i: InteractorI) => (state: InteractorInterface, action: AppAction): InteractorInterface => {
    if (isInteractorAction(action) && i == action.i) {
        return {
            protein: {
                query: qprotein(state.protein.query, action),
            },
            editing: editing(state.editing, action),
            processing: processing(state.processing, action),
            alignment: {
                query: qalignment(state.alignment.query, action),
                current: current(state.alignment.current, action),
            }
        }
    }

    return state
}

const qprotein = (state: string, action: InteractorAction): string => {
    switch (action.type) {
        case AppActionTypes.UPDATE_PROTEIN_QUERY:
            return action.query
        case AppActionTypes.RESET_INTERACTOR:
            return ''
        default:
            return state
    }
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

const qalignment = (state: string, action: InteractorAction): string => {
    switch (action.type) {
        case AppActionTypes.UPDATE_ALIGNMENT_QUERY:
            return action.query
        case AppActionTypes.SELECT_PROTEIN:
            return ''
        case AppActionTypes.UNSELECT_PROTEIN:
            return ''
        case AppActionTypes.ADD_ALIGNMENT:
            return ''
        case AppActionTypes.RESET_INTERACTOR:
            return ''
        default:
            return state
    }
}

const current = (state: Alignment, action: InteractorAction): Alignment => {
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

const feedback = (_, action: AppAction): Feedback => {
    switch (action.type) {
        case AppActionTypes.SHOW_FEEDBACK:
            return { success: action.success, errors: action.errors }
        default:
            return null
    }
}
