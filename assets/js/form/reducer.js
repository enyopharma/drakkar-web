import actions from './actions'

const method = (state = null, action) => {
    if (action.type == actions.RESET) return null

    switch (action.type) {
        case actions.SELECT_METHOD:
            return action.method
        case actions.UNSELECT_METHOD:
            return null
        default:
            return state
    }
}

const editing = (i, state = false, action) => {
    if (action.type == actions.RESET) return false

    if (action.i != i) return state

    switch (action.type) {
        case actions.SELECT_PROTEIN:
            return action.protein.type == 'v'
        case actions.EDIT_MATURE:
            return true
        case actions.UPDATE_MATURE:
            return false
        default:
            return state
    }
}

const processing = (i, state = false, action) => {
    if (action.type == actions.RESET) return false

    if (action.i != i) return state

    switch (action.type) {
        case actions.FIRE_ALIGNMENT:
            return true
        case actions.ADD_ALIGNMENT:
            return false
        case actions.CANCEL_ALIGNMENT:
            return false
        default:
            return state
    }
}

const protein = (i, state = null, action) => {
    if (action.type == actions.RESET) return null

    if (action.i != i) return state

    switch (action.type) {
        case actions.SELECT_PROTEIN:
            return action.protein
        case actions.UNSELECT_PROTEIN:
            return null
        default:
            return state
    }
}

const name = (i, state = '', action) => {
    if (action.type == actions.RESET) return ''

    if (action.i != i) return state

    switch (action.type) {
        case actions.SELECT_PROTEIN:
            return action.protein.type == 'h' || action.protein.matures.length == 0
                ? action.protein.name
                : ''
        case actions.UNSELECT_PROTEIN:
            return ''
        case actions.UPDATE_MATURE:
            return action.mature.name
        default:
            return state
    }
}

const start = (i, state = '', action) => {
    if (action.type == actions.RESET) return ''

    if (action.i != i) return state

    switch (action.type) {
        case actions.SELECT_PROTEIN:
            return action.protein.type == 'h' || action.protein.matures.length == 0
                ? 1
                : ''
        case actions.UNSELECT_PROTEIN:
            return ''
        case actions.UPDATE_MATURE:
            return action.mature.start
        default:
            return state
    }
}

const stop = (i, state = '', action) => {
    if (action.type == actions.RESET) return ''

    if (action.i != i) return state

    switch (action.type) {
        case actions.SELECT_PROTEIN:
            return action.protein.type == 'h' || action.protein.matures.length == 0
                ? action.protein.sequence.length
                : ''
        case actions.UNSELECT_PROTEIN:
            return ''
        case actions.UPDATE_MATURE:
            return action.mature.stop
        default:
            return state
    }
}

const mapping = (i, state = [], action) => {
    if (action.type == actions.RESET) return []

    if (action.i != i) return state

    switch (action.type) {
        case actions.SELECT_PROTEIN:
            return []
        case actions.UNSELECT_PROTEIN:
            return []
        case actions.UPDATE_MATURE:
            return []
        case actions.ADD_ALIGNMENT:
            return state.concat(action.alignment)
        case actions.REMOVE_ALIGNMENT:
            return [
                ...state.slice(0, action.index),
                ...state.slice(action.index + 1, state.length),
            ]
        default:
            return state
    }
}

const alignment = (i, state = null, action) => {
    if (action.type == actions.RESET) return null

    if (action.i != i) return state

    switch (action.type) {
        case actions.SHOW_ALIGNMENT:
            return action.alignment
        case actions.ADD_ALIGNMENT:
            return null
        case actions.CANCEL_ALIGNMENT:
            return null
        default:
            return state
    }
}

const interactor = (i, state = {}, action) => {
    return {
        editing: editing(i, state.editing, action),
        processing: processing(i, state.processing, action),
        protein: protein(i, state.protein, action),
        name: name(i, state.name, action),
        start: start(i, state.start, action),
        stop: stop(i, state.stop, action),
        mapping: mapping(i, state.mapping, action),
        alignment: alignment(i, state.alignment, action),
    }
}

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

export default (state = {}, action) => {
    return {
        method: method(state.method, action),
        interactor1: interactor(1, state.interactor1, action),
        interactor2: interactor(2, state.interactor2, action),
        saving: saving(state.saving, action),
        feedback: feedback(state.feedback, action),
    }
}
