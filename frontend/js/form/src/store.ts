import { createStore, applyMiddleware } from 'redux'
import thunk from 'redux-thunk';

import { AppState } from './state'

import { reducer } from './reducer'

export const create = (state: AppState) => createStore(reducer, state, applyMiddleware(thunk))
