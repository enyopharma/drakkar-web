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

    selectProtein: (i, protein) => {
        return {
            i: i,
            type: actions.SELECT_PROTEIN,
            protein: protein,
        }
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

    removeAlignment: (i, ix) => {
        return {
            i: i,
            type: actions.REMOVE_ALIGNMENT,
            ix: ix,
        }
    },

    removeIsoform: (i, ix, jx) => {
        return {
            i: i,
            type: actions.REMOVE_ISOFORM,
            ix: ix,
            jx: jx,
        }
    },

    removeOccurence: (i, ix, jx, kx) => {
        return {
            i: i,
            type: actions.REMOVE_OCCURENCE,
            ix: ix,
            jx: jx,
            kx: kx,
        }
    },
}
