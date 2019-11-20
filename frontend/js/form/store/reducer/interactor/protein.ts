import { AppActionTypes, InteractorAction } from '../../actions'

import { ProteinState } from '../../../types'

const query = (state: string, action: InteractorAction): string => {
    switch (action.type) {
        case AppActionTypes.UPDATE_PROTEIN_QUERY:
            return action.query
        case AppActionTypes.RESET_INTERACTOR:
            return ''
        default:
            return state
    }
}

const accession = (state: string, action: InteractorAction): string => {
    switch (action.type) {
        case AppActionTypes.SELECT_PROTEIN:
            return action.protein.accession
        case AppActionTypes.UNSELECT_PROTEIN:
            return null
        case AppActionTypes.RESET_INTERACTOR:
            return null
        default:
            return state
    }
}

export const protein = (state: ProteinState, action: InteractorAction): ProteinState => {
    return {
        query: query(state.query, action),
        accession: accession(state.accession, action),
    }
}
