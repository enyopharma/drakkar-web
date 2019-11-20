import React from 'react'

import { Provider } from 'react-redux'
import { create } from '../store/create'
import { connect } from '../store/connect'
import { AppProps } from '../store/connect'

import { AppState, DescriptionType } from '../types'

import { MethodFieldset } from './MethodFieldset'
import { ActionsFieldset } from './ActionsFieldset'
import { InteractorFieldset } from './InteractorFieldset'

const StatelessForm: React.FC<AppProps> = ({ method, interactor1, interactor2, actions }) => {
    return (
        <form onSubmit={e => e.preventDefault()}>
            <MethodFieldset {...method} />
            <InteractorFieldset {...interactor1} />
            <InteractorFieldset {...interactor2} />
            <ActionsFieldset {...actions} />
        </form>
    )
}

const StatefulForm = connect(StatelessForm);

export const Form = (wrapper: string, type: DescriptionType, run_id: number, pmid: number, state: AppState) => (
    <Provider store={create(state)}>
        <StatefulForm type={type} run_id={run_id} pmid={pmid} wrapper={wrapper} />
    </Provider>
)
