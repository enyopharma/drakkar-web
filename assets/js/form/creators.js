import api from './api'
import actions from './actions'

const method = {
    update: query => {
        return {
            type: actions.UPDATE_METHOD_QUERY,
            query: query,
        }
    },

    select: psimi_id => {
        return (dispatch) => {
            api.methods.select(psimi_id).then(method => {
                dispatch({
                    type: actions.SELECT_METHOD,
                    method: method,
                })
            })
        }
    },

    unselect: () => {
        return {
            type: actions.UNSELECT_METHOD,
        }
    },
}

const protein = {
    update: (i, query) => {
        return {
            i: i,
            type: actions.UPDATE_PROTEIN_QUERY,
            query: query,
        }
    },

    select: (i, accession) => {
        return (dispatch) => {
            api.proteins.select(accession).then(protein => {
                dispatch({
                    i: i,
                    type: actions.SELECT_PROTEIN,
                    protein: protein,
                })
            })
        }
    },

    unselect: i => {
        return {
            i: i,
            type: actions.UNSELECT_PROTEIN,
        }
    },
}

const mature = {
    edit: i => {
        return {
            i: i,
            type: actions.EDIT_MATURE,
        }
    },

    update: (i, mature) => {
        return {
            i: i,
            type: actions.UPDATE_MATURE,
            mature: mature,
        }
    },
}

const alignment = {
    update: (i, query) => {
        return {
            i: i,
            type: actions.UPDATE_ALIGNMENT_QUERY,
            query: query,
        }
    },

    fire: (i, query, sequences) => {
        return (dispatch) => {
            dispatch({
                i: i,
                type: actions.FIRE_ALIGNMENT,
            })

            api.alignment(query, sequences)
                .catch(error => console.log(error))
                .then(alignment => dispatch({
                    i: i,
                    type: actions.SHOW_ALIGNMENT,
                    alignment: alignment
                }))
        }
    },

    add: (i, alignment) => {
        return {
            i: i,
            type: actions.ADD_ALIGNMENT,
            alignment: alignment,
        }
    },

    remove: (i, index) => {
        return {
            i: i,
            type: actions.REMOVE_ALIGNMENT,
            index: index,
        }
    },

    cancel: i => {
        return {
            i: i,
            type: actions.CANCEL_ALIGNMENT
        }
    }
}

const save = (run_id, pmid) => {
    return (dispatch, getState) =>  {
        dispatch({ type: actions.FIRE_SAVE })

        api.save(run_id, pmid, getState()).then(json => dispatch({
            type: actions.SHOW_FEEDBACK,
            success: json.success,
            message: json.reason,
        }))
    }
}

const reset = () => {
    return { type: actions.RESET }
}

export default { method, protein, mature, alignment, save, reset }
