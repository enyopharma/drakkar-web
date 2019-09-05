import actions from '../actions'

import method from './method'
import interactor from './interactor'

const saving = (state = false, action) => {
    switch (action.type) {
        case actions.FIRE_SAVE:
            return true
        case actions.SHOW_FEEDBACK:
            return false
        default:
            return state
    }
}

const feedback = (state = null, action) => {
    switch (action.type) {
        case actions.SHOW_FEEDBACK:
            return { success: action.success, message: action.message }
        default:
            return null
    }
}

const ui = (state = {}, action) => {
    return {
        saving: saving(state.saving, action),
        feedback: feedback(state.feedback, action),
    }
}

export default (state = {}, action) => {
    return {
        method: method(state.method, action),
        interactor1: interactor(1, state.interactor1, action),
        interactor2: interactor(2, state.interactor2, action),
        ui: ui(state.ui, action),
    }
}
