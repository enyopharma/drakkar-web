import actions from '../actions'

import protein from './protein'
import alignment from './alignment'

const name = (state = '', action) => {
    switch (action.type) {
        case actions.SELECT_PROTEIN:
            return action.protein.type == 'h' || action.protein.matures.length == 0
                ? action.protein.name
                : ''
        case actions.UNSELECT_PROTEIN:
            return ''
        case actions.UPDATE_MATURE:
            return action.mature.name
        case actions.RESET:
            return ''
        default:
            return state
    }
}

const start = (state = '', action) => {
    switch (action.type) {
        case actions.SELECT_PROTEIN:
            return action.protein.type == 'h' || action.protein.matures.length == 0
                ? 1
                : ''
        case actions.UNSELECT_PROTEIN:
            return ''
        case actions.UPDATE_MATURE:
            return action.mature.start
        case actions.RESET:
            return ''
        default:
            return state
    }
}

const stop = (state = '', action) => {
    switch (action.type) {
        case actions.SELECT_PROTEIN:
            return action.protein.type == 'h' || action.protein.matures.length == 0
                ? action.protein.sequence.length
                : ''
        case actions.UNSELECT_PROTEIN:
            return ''
        case actions.UPDATE_MATURE:
            return action.mature.stop
        case actions.RESET:
            return ''
        default:
            return state
    }
}

const mapping = (state = [], action) => {
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
        case actions.RESET:
            return []
        default:
            return state
    }
}

const editing = (state = false, action) => {
    switch (action.type) {
        case actions.SELECT_PROTEIN:
            return action.protein.type == 'v'
        case actions.EDIT_MATURE:
            return true
        case actions.UPDATE_MATURE:
            return false
        case actions.RESET:
            return false
        default:
            return state
    }
}

const processing = (state = false, action) => {
    switch (action.type) {
        case actions.FIRE_ALIGNMENT:
            return true
        case actions.ADD_ALIGNMENT:
            return false
        case actions.CANCEL_ALIGNMENT:
            return false
        case actions.RESET:
            return false
        default:
            return state
    }
}

const ui = (state = {}, action) => {
    return {
        editing: editing(state.editing, action),
        processing: processing(state.processing, action),
        alignment: alignment(state.alignment, action),
    }
}

export default (i, state = {}, action) => {
    const scoped = i != action.i && action.type != actions.RESET
        ? { type: 'OUT_OF_SCOPE' }
        : action

    return {
        protein: protein(state.protein, scoped),
        name: name(state.name, scoped),
        start: start(state.start, scoped),
        stop: stop(state.stop, scoped),
        mapping: mapping(state.mapping, scoped),
        ui: ui(state.ui, scoped),
    }
}
