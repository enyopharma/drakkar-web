import { AppActionTypes, InteractorAction } from '../../actions'

import { InteractorUiState } from '../../../types'

import { alignment } from './ui/alignment'

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

export const ui = (state: InteractorUiState, action: InteractorAction): InteractorUiState => {
    return {
        editing: editing(state.editing, action),
        processing: processing(state.processing, action),
        alignment: alignment(state.alignment, action),
    }
}
