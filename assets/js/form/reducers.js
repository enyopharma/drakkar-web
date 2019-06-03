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

    start: (state = 0, action) => {
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

    stop: (state = 0, action) => {
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

    mapping: (state = [], action) => {
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
                    ...state.slice(0, action.ix),
                    ...state.slice(action.ix + 1, state.length),
                ]
            case actions.REMOVE_ISOFORM: {
                const alignment = state[action.ix]

                return [
                    ...state.slice(0, action.ix),
                    ...(alignment.isoforms.length == 1 ? [] : [{
                        sequence: alignment.sequence,
                        isoforms: [
                            ...alignment.isoforms.slice(0, action.jx),
                            ...alignment.isoforms.slice(action.jx + 1, alignment.isoforms.length),
                        ],
                    }]),
                    ...state.slice(action.ix + 1, state.length),
                ]
            }
            case actions.REMOVE_OCCURENCE: {
                const alignment = state[action.ix]
                const isoforms = alignment.isoforms
                const isoform = isoforms[action.jx]

                const newIsoforms = [
                    ...isoforms.slice(0, action.jx),
                    ...(isoform.occurences.length == 1 ? [] : [{
                        accession: isoform.accession,
                        occurences: [
                            ...isoform.occurences.slice(0, action.kx),
                            ...isoform.occurences.slice(action.kx + 1, isoform.occurences.length),
                        ],
                    }]),
                    ...isoforms.slice(action.jx + 1, isoforms.length),
                ]

                return [
                    ...state.slice(0, action.ix),
                    ...(newIsoforms.length == 0 ? [] : [{
                        sequence: alignment.sequence,
                        isoforms: newIsoforms,
                    }]),
                    ...state.slice(action.ix + 1, state.length),
                ]
            }
            default:
                return state
        }
    },
}

export default reducers;
