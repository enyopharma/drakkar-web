import React from 'react'
import { Provider } from 'react-redux'
import { configureStore } from '@reduxjs/toolkit'

import { connect } from '../src/props'
import { reducer } from '../src/reducer'

import { DescriptionType, Description } from '../src/types'

import { Form } from './Form'

const StatefulForm = connect(Form);

export const App = (type: DescriptionType, run_id: number, pmid: number, description: Description | null) => {
    const preloadedState = description === null ? undefined : {
        description: {
            method: description.method,
            interactor1: description.interactor1,
            interactor2: description.interactor2,
        },
    }

    const store = configureStore({ reducer, preloadedState })

    return (
        <Provider store={store}>
            <StatefulForm type={type} run_id={run_id} pmid={pmid} />
        </Provider>
    )
}
