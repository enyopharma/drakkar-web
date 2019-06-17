import actions from './actions'

const qmethod = (state = '', action) => {
    switch (action.type) {
        case actions.UPDATE_METHOD_QUERY:
            return action.query
        case actions.RESET:
            return ''
        default:
            return state
    }
}

const method = (state = null, action) => {
    switch (action.type) {
        case actions.SELECT_METHOD:
            return {
                psimi_id: action.method.psimi_id,
                name: action.method.name,
            }
        case actions.UNSELECT_METHOD:
            return null
        case action.RESET:
            return null
        default:
            return state
    }
}

const editing = (i, state = false, action) => {
    if (action.i != i) return state

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

const processing = (i, state = false, action) => {
    if (action.i != i) return state

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

const qprotein = (i, state = '', action) => {
    if (action.i != i) return state

    switch (action.type) {
        case actions.UPDATE_PROTEIN_QUERY:
            return action.query
        case actions.RESET:
            return ''
        default:
            return state
    }
}

const protein = (i, state = null, action) => {
    if (action.i != i) return state

    switch (action.type) {
        case actions.SELECT_PROTEIN:
            return {
                type: action.protein.type,
                accession: action.protein.accession,
                name: action.protein.name,
                description: action.protein.description,
                sequence: action.protein.sequence,
                isoforms: action.protein.isoforms,
                matures: action.protein.matures,
                chains: action.protein.chains,
                domains: action.protein.domains,
            }
        case actions.UNSELECT_PROTEIN:
            return null
        case actions.RESET:
            return null
        default:
            return state
    }
}

const name = (i, state = '', action) => {
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
        case actions.RESET:
            return ''
        default:
            return state
    }
}

const start = (i, state = '', action) => {
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
        case actions.RESET:
            return ''
        default:
            return state
    }
}

const stop = (i, state = '', action) => {
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
        case actions.RESET:
            return ''
        default:
            return state
    }
}

const mapping = (i, state = [], action) => {
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
        case actions.RESET:
            return []
        default:
            return state
    }
}

const qalignment = (i, state = '', action) => {
    if (action.i != i) return state

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

const alignment = (i, state = null, action) => {
    if (action.i != i) return state

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

const interactor = (i, state = {}, action) => {
    return {
        editing: editing(i, state.editing, action),
        processing: processing(i, state.processing, action),
        qprotein: qprotein(i, state.qprotein, action),
        protein: protein(i, state.protein, action),
        name: name(i, state.name, action),
        start: start(i, state.start, action),
        stop: stop(i, state.stop, action),
        mapping: mapping(i, state.mapping, action),
        qalignment: qalignment(i, state.qalignment, action),
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
        qmethod: qmethod(state.qmethod, action),
        method: method(state.method, action),
        interactor1: interactor(1, state.interactor1, action),
        interactor2: interactor(2, state.interactor2, action),
        saving: saving(state.saving, action),
        feedback: feedback(state.feedback, action),
    }
}
