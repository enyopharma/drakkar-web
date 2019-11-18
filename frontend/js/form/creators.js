import api from './api'
import actions from './actions'

export const updateMethodQuery = query => ({
    type: actions.UPDATE_METHOD_QUERY,
    query: query,
})

export const selectMethod = psimi_id => dispatch => {
    api.methods.select(psimi_id).then(method => {
        dispatch({
            type: actions.SELECT_METHOD,
            method: method,
        })
    })
}

export const unselectMethod = () => ({
    type: actions.UNSELECT_METHOD,
})

export const updateProteinQuery = (i, query) => ({
    i: i,
    type: actions.UPDATE_PROTEIN_QUERY,
    query: query,
})

export const selectProtein = (i, accession) => dispatch => {
    api.proteins.select(accession).then(protein => {
        dispatch({
            i: i,
            type: actions.SELECT_PROTEIN,
            protein: protein,
        })
    })
}

export const unselectProtein = i => ({
    i: i,
    type: actions.UNSELECT_PROTEIN,
})

export const editMature = i => ({
    i: i,
    type: actions.EDIT_MATURE,
})

export const updateMature = (i, mature) => ({
    i: i,
    type: actions.UPDATE_MATURE,
    mature: mature,
})

export const updateAlignmentQuery = (i, query) => ({
    i: i,
    type: actions.UPDATE_ALIGNMENT_QUERY,
    query: query,
})

export const fireAlignment = (i, query, sequences) => dispatch => {
    dispatch({ i: i, type: actions.FIRE_ALIGNMENT })

    api.alignment(query, sequences)
        .catch(error => console.log(error))
        .then(alignment => dispatch({
            i: i,
            type: actions.SHOW_ALIGNMENT,
            alignment: alignment
        }))
}

export const addAlignment = (i, alignment) => ({
    i: i,
    type: actions.ADD_ALIGNMENT,
    alignment: alignment,
})

export const removeAlignment = (i, index) => ({
    i: i,
    type: actions.REMOVE_ALIGNMENT,
    index: index,
})

export const cancelAlignment = i => ({
    i: i,
    type: actions.CANCEL_ALIGNMENT
})

export const saveDescription = (run_id, pmid) => (dispatch, getState) => {
    dispatch({ type: actions.FIRE_SAVE })

    api.save(run_id, pmid, getState()).then(json => dispatch({
        type: actions.SHOW_FEEDBACK,
        success: json.success,
        errors: json.reason ? [json.reason] : json.errors,
    }))
}

export const resetForm = () => ({
    type: actions.RESET,
})
