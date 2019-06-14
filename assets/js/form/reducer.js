import actions from './actions'

const reducers = {
    ui: {
        saving: (state = false, action) => {
            switch (action.type) {
                case actions.FIRE_SAVE:
                    return true
                case actions.SHOW_FEEDBACK:
                    return false
                default:
                    return state
            }
        },

        feedback: (state = null, action) => {
            switch (action.type) {
                case actions.SHOW_FEEDBACK:
                    return { success: action.success, message: action.message }
                default:
                    return null
            }
        },

        interactor: (i, state = {}, action) => {
            return {
                editing: reducers.ui.editing(i, state.editing, action),
                processing: reducers.ui.processing(i, state.processing, action),
                alignment: reducers.ui.alignment(i, state.alignment, action),
            }
        },

        editing: (i, state = false, action) => {
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
        },

        processing: (i, state = false, action) => {
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
        },

        alignment: (i, state = null, action) => {
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
        },
    },

    data: {
        method: (state = null, action) => {
            if (action.type == actions.RESET) return null

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
                protein: reducers.data.protein(i, state.protein, action),
                name: reducers.data.name(i, state.name, action),
                start: reducers.data.start(i, state.start, action),
                stop: reducers.data.stop(i, state.stop, action),
                mapping: reducers.data.mapping(i, state.mapping, action),
            }
        },

        protein: (i, state = null, action) => {
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
        },

        name: (i, state = '', action) => {
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
        },

        start: (i, state = '', action) => {
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
        },

        stop: (i, state = '', action) => {
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
        },

        mapping: (i, state = [], action) => {
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
        },
    },
}

const ui = (state = {}, action) => {
    return {
        saving: reducers.ui.saving(state.saving, action),
        feedback: reducers.ui.feedback(state.feedback, action),
        interactor1: reducers.ui.interactor(1, state.interactor1, action),
        interactor2: reducers.ui.interactor(2, state.interactor2, action),
    }
}

const data = (state = {}, action) => {
    return {
        method: reducers.data.method(state.method, action),
        interactor1: reducers.data.interactor(1, state.interactor1, action),
        interactor2: reducers.data.interactor(2, state.interactor2, action),
    }
}

export default (state, action) => {
    return {
        run_id: state.run_id,
        pmid: state.pmid,
        ui: ui(state.ui, action),
        data: data(state.data, action),
    }
};
