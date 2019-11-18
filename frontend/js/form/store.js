import { createStore, applyMiddleware } from 'redux'
import thunk from 'redux-thunk';
import reducer from './reducers/form'

export const store = state => createStore(reducer, state, applyMiddleware(thunk))
