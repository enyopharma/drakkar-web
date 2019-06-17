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

const saving = (state = false, action) => {
    switch (action.type) {
        case actions.FIRE_SAVE:
            return true
        case actions.HANDLE_SAVE:
            return false
        default:
            return state
    }
}

const feedback = (state = null, action) => {
    switch (action.type) {
        case actions.HANDLE_SAVE:
            return { success: action.success, message: action.message }
        default:
            return null
    }
}

const formui = (state = {}, action) => {
    return {
        qmethod: qmethod(state.qmethod, action),
        saving: saving(state.saving, action),
        feedback: feedback(state.feedback, action),
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
        case actions.RESET:
            return null
        default:
            return state
    }
}

const protein = (state = null, action) => {
    switch (action.type) {
        case actions.SELECT_PROTEIN:
            return {
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

const qprotein = (state = '', action) => {
    switch (action.type) {
        case actions.UPDATE_PROTEIN_QUERY:
            return action.query
        case actions.RESET:
            return ''
        default:
            return state
    }
}

const qalignment = (state = '', action) => {
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

const alignment = (state = null, action) => {
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

const interactorui = (state = {}, action) => {
    return {
        editing: editing(state.editing, action),
        processing: processing(state.processing, action),
        qprotein: qprotein(state.qprotein, action),
        qalignment: qalignment(state.qalignment, action),
        alignment: alignment(state.alignment, action),
    }
}

const interactor = (i, state = {}, action) => {
    const scoped = i != action.i && action.type != actions.RESET
        ? { type: 'OUT_OF_SCOPE' }
        : action

    const newState = {
        protein: protein(state.protein, scoped),
        name: name(state.name, scoped),
        start: start(state.start, scoped),
        stop: stop(state.stop, scoped),
        mapping: mapping(state.mapping, scoped),
        ui: interactorui(state.ui, scoped)
    }

    if (action.type == actions.HANDLE_SAVE && action.success) {
        newState.protein.matures = newState.protein.matures.concat([{
            name: newState.name,
            start: newState.start,
            stop: newState.stop,
        }])
    }

    return newState
}

export default (state = {}, action) => {
    return {
        method: method(state.method, action),
        interactor1: interactor(1, state.interactor1, action),
        interactor2: interactor(2, state.interactor2, action),
        ui: formui(state.ui, action),
    }
}
