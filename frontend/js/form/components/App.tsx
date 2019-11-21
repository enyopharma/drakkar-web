import React from 'react'
import { Provider } from 'react-redux'

import { create } from '../src/store'
import { connect } from '../src/props'

import { AppState } from '../src/state'
import { AppProps } from '../src/props'
import { DescriptionType } from '../src/types'

import { MethodFieldset } from './MethodFieldset'
import { SubmitFieldset } from './SubmitFieldset'
import { InteractorFieldset } from './InteractorFieldset'

const StatelessForm: React.FC<AppProps> = ({ method, interactor1, interactor2, submit, actions }) => {
    return (
        <form onSubmit={e => e.preventDefault()}>
            <MethodFieldset {...method} {...actions.method} />
            <InteractorFieldset {...interactor1} {...actions.interactor1} />
            <InteractorFieldset {...interactor2} {...actions.interactor2} />
            <SubmitFieldset {...submit} {...actions.submit} />
        </form>
    )
}

const StatefulForm = connect(StatelessForm);

export const App = (wrapper: string, type: DescriptionType, run_id: number, pmid: number, state: AppState) => (
    <Provider store={create(state)}>
        <StatefulForm type={type} run_id={run_id} pmid={pmid} wrapper={wrapper} />
    </Provider>
)
