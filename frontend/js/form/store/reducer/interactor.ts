import { AppActionTypes, AppAction, InteractorAction, isInteractorAction } from '../actions'

import { InteractorState, InteractorI, Alignment } from '../../types'

import { protein } from './interactor/protein'
import { ui } from './interactor/ui'

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

export const interactor = (i: InteractorI) => {
    return (state: InteractorState, action: AppAction): InteractorState => {
        if (isInteractorAction(action) && action.i == i) {
            return {
                i: i,
                protein: protein(state.protein, action),
                name: name(state.name, action),
                start: start(state.start, action),
                stop: stop(state.stop, action),
                mapping: mapping(state.mapping, action),
                ui: ui(state.ui, action),
            }
        }

        return state
    }
}
