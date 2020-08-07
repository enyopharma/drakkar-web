import React from 'react'
import { Provider } from 'react-redux'
import { configureStore } from '@reduxjs/toolkit'
import { reducer } from '../src/reducer'
import { DescriptionType, Description } from '../src/types'

import { Form } from './Form'

export const App = (type: DescriptionType, run_id: number, pmid: number, description: Description | null) => {
    const preloadedState = {
        run_id: run_id,
        pmid: pmid,
        type: type,
        description: description == null ? undefined : description,
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
