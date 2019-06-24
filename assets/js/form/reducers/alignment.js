import actions from '../actions'

const query = (state = '', action) => {
    switch (action.type) {
        case actions.UPDATE_ALIGNMENT_QUERY:
            return action.query
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
            return Object.assign(action.alignment, {
                isoforms: action.alignment.isoforms
                    .sort((a, b) => a.accession.localeCompare(b.accession))
                    .map(isoform => Object.assign(isoform, {
                        occurences: isoform.occurences.sort((a, b) => a.start - b.start)
                    })
                )
            })
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
