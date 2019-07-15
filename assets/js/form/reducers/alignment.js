import actions from '../actions'

const query = (state = '', action) => {
    switch (action.type) {
        case actions.UPDATE_ALIGNMENT_QUERY:
            return action.query
        case actions.SELECT_PROTEIN:
            return ''
        case actions.UNSELECT_PROTEIN:
            return ''
        case actions.ADD_ALIGNMENT:
            return ''
        case actions.RESET:
            return ''
        default:
            return state
    }
}

const current = (state = null, action) => {
    switch (action.type) {
        case actions.SHOW_ALIGNMENT:
            return action.alignment
        case actions.ADD_ALIGNMENT:
            return null
        case actions.CANCEL_ALIGNMENT:
            return null
        case actions.RESET:
            return null
        default:
            return state
    }
}

export default (state = {}, action) => {
    return {
        query: query(state.query, action),
        current: current(state.current, action),
    }
}
