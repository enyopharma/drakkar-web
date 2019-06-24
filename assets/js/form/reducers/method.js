import actions from '../actions'

const query = (state = '', action) => {
    switch (action.type) {
        case actions.UPDATE_METHOD_QUERY:
            return action.query
        case actions.RESET:
            return ''
        default:
            return state
    }
}

const psimi_id = (state = null, action) => {
    switch (action.type) {
        case actions.SELECT_METHOD:
            return action.method.psimi_id
        case actions.UNSELECT_METHOD:
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
        psimi_id: psimi_id(state.psimi_id, action),
    }
}
