import { createStore, applyMiddleware } from 'redux'
import thunk from 'redux-thunk';
import { reducer } from './reducer'

import { AppState } from '../types'

export const create = (state: AppState) => createStore(reducer, state, applyMiddleware(thunk))
