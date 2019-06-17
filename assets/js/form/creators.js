import api from './api'
import actions from './actions'
import { state2sequences } from './state2props'

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

    fire: i => {
        return (dispatch, getState) => {
            const state = getState()
            const interactor = i == 1 ? state.interactor1 : state.interactor2
            const query = interactor.ui.qalignment
            const sequences = state2sequences(interactor)

            dispatch({ i: i, type: actions.FIRE_ALIGNMENT})

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

        api.save(run_id, pmid, getState())
            .catch(error => console.log(error))
            .then(response => response.json())
            .then(json => dispatch({
                type: actions.SHOW_FEEDBACK,
                success: json.success,
                message: json.reason,
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
