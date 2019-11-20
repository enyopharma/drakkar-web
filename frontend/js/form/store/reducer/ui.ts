import { AppActionTypes, AppAction } from '../actions'

import { AppUiState, Feedback } from '../../types'

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

export const ui = (state: AppUiState, action: AppAction): AppUiState => {
    return {
        saving: saving(state.saving, action),
        feedback: feedback(state.feedback, action),
    }
}
