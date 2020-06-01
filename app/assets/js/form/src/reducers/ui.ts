import { AppActionTypes, AppAction } from '../actions'
import { InteractorAction, isInteractorAction } from '../actions'
import { UI, InteractorUI } from '../state'
import { Method, Protein, InteractorI, Feedback, Alignment } from '../types'

export const ui = (state: UI, action: AppAction): UI => {
    return {
        init: init(state.init, action),
        query: qmethod(state.query, action),
        method: method(state.method, action),
        interactor1: interactor(1)(state.interactor1, action),
        interactor2: interactor(2)(state.interactor2, action),
        saving: saving(state.saving, action),
        feedback: feedback(state.feedback, action),
    }
}

const init = (state: boolean, action: AppAction): boolean => {
    switch (action.type) {
        case AppActionTypes.SHOW_FORM:
            return true
        default:
            return state
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

const method = (state: Method | null, action: AppAction): Method | null => {
    switch (action.type) {
        case AppActionTypes.INIT_METHOD:
            return action.method
        case AppActionTypes.SELECT_METHOD:
            return action.method
        case AppActionTypes.UNSELECT_METHOD:
            return null
        case AppActionTypes.RESET_FORM:
            return null
        default:
            return state
    }
}

const interactor = (i: InteractorI) => (state: InteractorUI, action: AppAction): InteractorUI => {
    if (isInteractorAction(action) && i == action.i) {
        return {
            query: qprotein(state.query, action),
            protein: protein(state.protein, action),
            editing: editing(state.editing, action),
            processing: processing(state.processing, action),
            alignment: alignment(state.alignment, action),
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

const protein = (state: Protein | null, action: InteractorAction): Protein | null => {
    switch (action.type) {
        case AppActionTypes.INIT_PROTEIN:
            return action.protein
        case AppActionTypes.SELECT_PROTEIN:
            return action.protein
        case AppActionTypes.UNSELECT_PROTEIN:
            return null
        case AppActionTypes.RESET_INTERACTOR:
            return null
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
