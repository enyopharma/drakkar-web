import { Reducer } from 'redux'

import { AppAction } from './actions'
import { AppState, init } from './state'

import { ui } from './ui'
import { description } from './description'

export const reducer: Reducer<AppState, AppAction> = (state: AppState | undefined = init, action: AppAction): AppState => {
    return {
        ui: ui(state.ui, action),
        description: description(state.description, action),
    }
}
