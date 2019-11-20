import { AppAction } from './actions'

import { AppState } from '../types'

import { method } from './reducer/method'
import { interactor } from './reducer/interactor'
import { ui } from './reducer/ui'

export const reducer = (state: AppState, action: AppAction): AppState => {
    return {
        method: method(state.method, action),
        interactor1: interactor(1)(state.interactor1, action),
        interactor2: interactor(2)(state.interactor2, action),
        ui: ui(state.ui, action),
    }
}
