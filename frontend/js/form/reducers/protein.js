import actions from '../actions'

const query = (state = '', action) => {
    switch (action.type) {
        case actions.UPDATE_PROTEIN_QUERY:
            return action.query
        case actions.RESET:
            return ''
        default:
            return state
    }
}

const accession = (state = null, action) => {
    switch (action.type) {
        case actions.SELECT_PROTEIN:
            return action.protein.accession
        case actions.UNSELECT_PROTEIN:
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
        accession: accession(state.accession, action),
    }
}
