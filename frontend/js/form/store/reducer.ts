import { AppAction } from './actions'

import { AppState } from '../types'

import { description } from './description'
import { uinterface } from './uinterface'

export const reducer = (state: AppState, action: AppAction): AppState => {
    return {
        description: description(state.description, action),
        uinterface: uinterface(state.uinterface, action),
    }
}
