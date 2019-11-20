import { AppActionTypes, AppAction } from '../actions'

import { MethodState } from '../../types'

const query = (state: string, action: AppAction): string => {
    switch (action.type) {
        case AppActionTypes.UPDATE_METHOD_QUERY:
            return action.query
        case AppActionTypes.RESET_FORM:
            return ''
        default:
            return state
    }
}

const psimi_id = (state: string, action: AppAction): string => {
    switch (action.type) {
        case AppActionTypes.SELECT_METHOD:
            return action.method.psimi_id
        case AppActionTypes.UNSELECT_METHOD:
            return null
        case AppActionTypes.RESET_FORM:
            return null
        default:
            return state
    }
}

export const method = (state: MethodState, action: AppAction): MethodState => {
    return {
        query: query(state.query, action),
        psimi_id: psimi_id(state.psimi_id, action),
    }
}
