import React from 'react'
import { createStore, applyMiddleware } from 'redux'
import { Provider } from 'react-redux'
import thunk from 'redux-thunk'

import { connect } from '../src/props'
import { reducer } from '../src/reducer'

import { AppState } from '../src/state'
import { AppProps } from '../src/props'
import { initialState } from '../src/reducer'
import { DescriptionType, Description } from '../src/types'

import { FormInit } from './FormInit'


const StatelessApp: React.FC<AppProps> = (props) => {
    return <FormInit {...props} />
}

const StatefulApp = connect(StatelessApp);

export const App = (type: DescriptionType, run_id: number, pmid: number, description: Description | null) => {
    const preloadedState: AppState = {
        ui: initialState.ui,
        description: description == null
            ? initialState.description
            : description
    }

    const store = createStore(reducer, preloadedState, applyMiddleware(thunk))

    return (
        <Provider store={store}>
            <StatefulApp type={type} run_id={run_id} pmid={pmid} />
        </Provider>
    )
}
