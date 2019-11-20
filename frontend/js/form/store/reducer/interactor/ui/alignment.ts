import { AppActionTypes, InteractorAction } from '../../../actions'

import { AlignmentState, Alignment } from '../../../../types'

const query = (state: string, action: InteractorAction): string => {
    switch (action.type) {
        case AppActionTypes.UPDATE_ALIGNMENT_QUERY:
            return action.query
        case AppActionTypes.SELECT_PROTEIN:
            return ''
        case AppActionTypes.UNSELECT_PROTEIN:
            return ''
        case AppActionTypes.ADD_ALIGNMENT:
            return ''
        case AppActionTypes.RESET_INTERACTOR:
            return ''
        default:
            return state
    }
}

const current = (state: Alignment, action: InteractorAction): Alignment => {
    switch (action.type) {
        case AppActionTypes.SHOW_ALIGNMENT:
            return action.alignment
        case AppActionTypes.ADD_ALIGNMENT:
            return null
        case AppActionTypes.CANCEL_ALIGNMENT:
            return null
        case AppActionTypes.RESET_INTERACTOR:
            return null
        default:
            return state
    }
}

export const alignment = (state: AlignmentState, action: InteractorAction): AlignmentState => {
    return {
        query: query(state.query, action),
        current: current(state.current, action),
    }
}
