import { AppActionTypes, AppAction } from './actions'
import { InteractorAction, isInteractorAction } from './actions'
import { Description, InteractorI, Interactor, Alignment } from '../types'

export const description = (state: Description, action: AppAction): Description => {
    return {
        method: {
            psimi_id: psimi_id(state.method.psimi_id, action)
        },
        interactor1: interactor(1)(state.interactor1, action),
        interactor2: interactor(2)(state.interactor2, action),
    }
}

const psimi_id = (state: string, action: AppAction): string => {
    switch (action.type) {
        case AppActionTypes.SELECT_METHOD:
            return action.method.psimi_id
        case AppActionTypes.UNSELECT_METHOD:
            return null
        case AppActionTypes.RESET_FORM:
            return null
        default:
            return state
    }
}

const interactor = (i: InteractorI) => (state: Interactor, action: AppAction): Interactor => {
    if (isInteractorAction(action) && i == action.i) {
        return {
            protein: {
                accession: accession(state.protein.accession, action)
            },
            name: name(state.name, action),
            start: start(state.start, action),
            stop: stop(state.stop, action),
            mapping: mapping(state.mapping, action),
        }
    }

    return state
}

const accession = (state: string, action: InteractorAction): string => {
    switch (action.type) {
        case AppActionTypes.SELECT_PROTEIN:
            return action.protein.accession
        case AppActionTypes.UNSELECT_PROTEIN:
            return null
        case AppActionTypes.RESET_INTERACTOR:
            return null
        default:
            return state
    }
}

const name = (state: string, action: InteractorAction): string => {
    switch (action.type) {
        case AppActionTypes.SELECT_PROTEIN:
            return action.protein.type == 'h' ? action.protein.name : ''
        case AppActionTypes.UNSELECT_PROTEIN:
            return ''
        case AppActionTypes.UPDATE_MATURE:
            return action.mature.name
        case AppActionTypes.RESET_INTERACTOR:
            return ''
        default:
            return state
    }
}

const start = (state: number, action: InteractorAction): number => {
    switch (action.type) {
        case AppActionTypes.SELECT_PROTEIN:
            return action.protein.type == 'h' || action.protein.matures.length == 0
                ? 1
                : null
        case AppActionTypes.UNSELECT_PROTEIN:
            return null
        case AppActionTypes.UPDATE_MATURE:
            return action.mature.start
        case AppActionTypes.RESET_INTERACTOR:
            return null
        default:
            return state
    }
}

const stop = (state: number, action: InteractorAction): number => {
    switch (action.type) {
        case AppActionTypes.SELECT_PROTEIN:
            return action.protein.type == 'h' || action.protein.matures.length == 0
                ? action.protein.sequence.length
                : null
        case AppActionTypes.UNSELECT_PROTEIN:
            return null
        case AppActionTypes.UPDATE_MATURE:
            return action.mature.stop
        case AppActionTypes.RESET_INTERACTOR:
            return null
        default:
            return state
    }
}

const mapping = (state: Alignment[], action: InteractorAction): Alignment[] => {
    switch (action.type) {
        case AppActionTypes.SELECT_PROTEIN:
            return []
        case AppActionTypes.UNSELECT_PROTEIN:
            return []
        case AppActionTypes.UPDATE_MATURE:
            return []
        case AppActionTypes.ADD_ALIGNMENT:
            return state.concat(action.alignment)
        case AppActionTypes.REMOVE_ALIGNMENT:
            return [
                ...state.slice(0, action.index),
                ...state.slice(action.index + 1, state.length),
            ]
        case AppActionTypes.RESET_INTERACTOR:
            return []
        default:
            return state
    }
}
