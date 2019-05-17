import actions from './actions'

const reducers = {
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
        if (action.i == i) {
            return {
                protein: reducers.protein(state.protein, action),
                name: reducers.name(state.name, action),
                start: reducers.start(state.start, action),
                stop: reducers.stop(state.stop, action),
                mapping: reducers.mapping(state.mapping, action),
            }
        }
        return state
    },

    protein: (state = null, action) => {
        switch (action.type) {
            case actions.SELECT_PROTEIN:
                return action.protein
            case actions.UNSELECT_PROTEIN:
                return null
            default:
                return state
        }
    },

    name: (state = '', action) => {
        switch (action.type) {
            case actions.SELECT_PROTEIN:
                return action.protein.type == 'h'
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

    start: (state = 0, action) => {
        switch (action.type) {
            case actions.SELECT_PROTEIN:
                return action.protein.type == 'h' ? 1 : ''
            case actions.UNSELECT_PROTEIN:
                return ''
            case actions.UPDATE_MATURE:
                return action.mature.start
            default:
                return state
        }
    },

    stop: (state = 0, action) => {
        switch (action.type) {
            case actions.SELECT_PROTEIN:
                return action.protein.type == 'h'
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

    mapping: (state = [], action) => {
        switch (action.type) {
            case actions.SELECT_PROTEIN:
                return []
            case actions.UNSELECT_PROTEIN:
                return []
            case actions.ADD_ALIGNMENT:
                return state.concat(action.alignment)
            case actions.REMOVE_ALIGNMENT:
                let newState = [...state];
                newState.splice(action.index, 1);
                return newState;
            default:
                return state
        }
    },
}

export default reducers;
