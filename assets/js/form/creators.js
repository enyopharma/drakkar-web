import api from './api'
import actions from './actions'

export default {
    method: {
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
    },

    protein: {
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
    },

    mature: {
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
    },

    alignment: {
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
    },
}
