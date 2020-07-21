import React from 'react'
import { createStore, applyMiddleware } from 'redux'
import { Provider } from 'react-redux'
import thunk from 'redux-thunk'

import { connect } from '../src/props'
import { reducer } from '../src/reducer'

import { initialState } from '../src/reducer'
import { AppState, DescriptionType, Description } from '../src/types'

import { Form } from './Form'

const StatefulForm = connect(Form);

export const App = (type: DescriptionType, run_id: number, pmid: number, description: Description | null) => {
    const preloadedState: AppState = {
        ...initialState,
        description: description ?? initialState.description,
    }

    const store = createStore(reducer, preloadedState, applyMiddleware(thunk))

    return (
        <Provider store={store}>
            <StatefulForm type={type} run_id={run_id} pmid={pmid} />
        </Provider>
    )
}
