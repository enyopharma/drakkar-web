import fetch from 'cross-fetch'
import actions from './actions'

export default {
    selectMethod: method => {
        return {
            type: actions.SELECT_METHOD,
            method: method,
        }
    },

    unselectMethod: () => {
        return {
            type: actions.UNSELECT_METHOD,
        }
    },

    selectProtein: (i, protein) => (dispatch) => {
        fetch('/proteins/' + protein.accession)
            .then(response => response.json(), error => console.log(error))
            .then(json => dispatch({
                i: i,
                type: actions.SELECT_PROTEIN,
                protein: json.data.protein,
            }))
    },

    unselectProtein: i => {
        return {
            i: i,
            type: actions.UNSELECT_PROTEIN,
        }
    },

    updateMature: (i, mature) => {
        return {
            i: i,
            type: actions.UPDATE_MATURE,
            mature: mature,
        }
    },

    addAlignment: (i, alignment) => {
        return {
            i: i,
            type: actions.ADD_ALIGNMENT,
            alignment: alignment,
        }
    },

    removeAlignment: (i, index) => {
        return {
            i: i,
            type: actions.REMOVE_ALIGNMENT,
            index: index,
        }
    }
}
