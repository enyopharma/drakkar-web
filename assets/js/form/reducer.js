import actions from './actions'

const ui = {
    reducer: (state = {}, action) => {
        return {
            interactor1: ui.interactor(1, state.interactor1, action),
            interactor2: ui.interactor(2, state.interactor2, action),
        }
    },

    interactor: (i, state = {}, action) => {
        return {
            editing: ui.editing(i, state.editing, action),
            processing: ui.processing(i, state.processing, action),
            alignment: ui.alignment(i, state.alignment, action),
        }
    },

    editing: (i, state = false, action) => {
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
    },

    processing: (i, state = false, action) => {
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
    },

    alignment: (i, state = null, action) => {
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
    },
}

const data = {
    reducer: (state = {}, action) => {
        return {
            method: data.method(state.method, action),
            interactor1: data.interactor(1, state.interactor1, action),
            interactor2: data.interactor(2, state.interactor2, action),
        }
    },

    method: (state = null, action) => {
        switch (action.type) {
            case actions.SELECT_METHOD:
                return action.method
            case actions.UNSELECT_METHOD:
                return null
            default:
                return state
        }
    },

    interactor: (i, state = {}, action) => {
        return {
            protein: data.protein(i, state.protein, action),
            name: data.name(i, state.name, action),
            start: data.start(i, state.start, action),
            stop: data.stop(i, state.stop, action),
            mapping: data.mapping(i, state.mapping, action),
        }
    },

    protein: (i, state = null, action) => {
        if (action.i != i) return state

        switch (action.type) {
            case actions.SELECT_PROTEIN:
                return action.protein
            case actions.UNSELECT_PROTEIN:
                return null
            default:
                return state
        }
    },

    name: (i, state = '', action) => {
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
    },

    start: (i, state = '', action) => {
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
    },

    stop: (i, state = '', action) => {
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
    },

    mapping: (i, state = [], action) => {
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
    },
}

export default (state, action) => {
    return {
        ui: ui.reducer(state.ui, action),
        data: data.reducer(state.data, action),
    }
};
