import { AppState } from './state'
import { AppAction } from './actions'

import { ui } from './ui'
import { description } from './description'

export const reducer = (state: AppState, action: AppAction): AppState => {
    return {
        ui: ui(state.ui, action),
        description: description(state.description, action),
    }
}
