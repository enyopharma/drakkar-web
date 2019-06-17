import api from './api'
import actions from './actions'

const method = {
    select: method => {
        return {
            type: actions.SELECT_METHOD,
            method: method,
        }
    },

    unselect: () => {
        return {
            type: actions.UNSELECT_METHOD,
        }
    },
}

const protein = {
    select: (i, protein) => {
        return dispatch => {
            api.protein.select(protein.accession)
                .then(protein => {
                    dispatch({
                        i: i,
                        type: actions.SELECT_PROTEIN,
                        protein: protein,
                    })
                })
                .catch(error => console.log(error))
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
    fire: (i, query, sequences) => {
        return dispatch => {
            dispatch({ i: i, type: actions.FIRE_ALIGNMENT})

            api.alignment(query, sequences, alignment => {
                dispatch({ i: i, type: actions.SHOW_ALIGNMENT, alignment: alignment })
            })
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
        dispatch({
            type: actions.FIRE_SAVE,
        })

        const state = getState();

        console.log(state);

        api.save(run_id, pmid, state, response => dispatch({
            type: actions.SHOW_FEEDBACK,
            success: response.success,
            message: response.reason,
        }))
    }
}

const reset = () => {
    return {
        type: actions.RESET,
    }
}

export default { method, protein, mature, alignment, save, reset }
