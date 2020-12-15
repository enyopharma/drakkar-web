import React from 'react'
import { Provider } from 'react-redux'
import { configureStore } from '@reduxjs/toolkit'
import { reducer } from '../src/reducer'
import { DescriptionType, Description } from '../src/types'

import { Form } from './Form'

type AppProps = {
    type: DescriptionType
    run_id: number
    pmid: number
    description: Description | null
}

export const App: React.FC<AppProps> = ({ type, run_id, pmid, description }) => {
    const preloadedState = {
        run_id: run_id,
        pmid: pmid,
        type: type,
        description: description == null ? undefined : {
            stable_id: description.stable_id,
            method_id: description.method_id,
            interactor1: description.interactor1,
            interactor2: description.interactor2,
        },
        interactorUI1: undefined,
        interactorUI2: undefined,
        saving: false,
        feedback: null,
    }

    const store = configureStore({ reducer, preloadedState })

    return (
        <Provider store={store}>
            <Form />
        </Provider>
    )
}
