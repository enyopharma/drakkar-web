import api from './api'
import actions from './actions'

const method = {
    update: query => {
        return {
            type: actions.UPDATE_METHOD_QUERY,
            query: query,
        }
    },

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
    update: (i, query) => {
        return {
            i: i,
            type: actions.UPDATE_PROTEIN_QUERY,
            query: query,
        }
    },

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
    update: (i, query) => {
        return {
            i: i,
            type: actions.UPDATE_ALIGNMENT_QUERY,
            query: query,
        }
    },

    fire: (i, sequences) => {
        return (dispatch, getState) => {
            const state = getState()
            const interactor = i == 1 ? state.interactor1 : state.interactor2
            const query = interactor.qalignment

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
    return dispatch => {
        dispatch({i: 1, type: actions.RESET})
        dispatch({i: 2, type: actions.RESET})
    }
}

export default { method, protein, mature, alignment, save, reset }
