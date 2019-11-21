import { AppAction } from './actions'

import { AppState } from '../types'

import { ui } from './ui'
import { description } from './description'

export const reducer = (state: AppState, action: AppAction): AppState => {
    return {
        ui: ui(state.ui, action),
        description: description(state.description, action),
    }
}
