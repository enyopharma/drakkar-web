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

    removeMapping: (i, ...idxs) => {
        if (idxs.length == 1) {
            return {
                i: i,
                type: actions.REMOVE_ALIGNMENT,
                ix: idxs[0],
            }
        }

        if (idxs.length == 2) {
            return {
                i: i,
                type: actions.REMOVE_ISOFORM,
                ix: idxs[0],
                jx: idxs[1],
            }
        }

        if (idxs.length == 3) {
            return {
                i: i,
                type: actions.REMOVE_OCCURENCE,
                ix: idxs[0],
                jx: idxs[1],
                kx: idxs[2],
            }
        }

        return {}
    },
}
