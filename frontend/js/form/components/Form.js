import React from 'react'

import { Provider } from 'react-redux'
import { store } from './../store'
import { connect } from './../connect'

import MethodFieldset from './MethodFieldset'
import ActionsFieldset from './ActionsFieldset'
import InteractorFieldset from './InteractorFieldset'

const StatelessForm = ({ method, interactor1, interactor2, actions }) => {
    return (
        <form onSubmit={e => e.preventDefault()}>
            <MethodFieldset {...method} />
            <InteractorFieldset {...interactor1} />
            <InteractorFieldset {...interactor2} />
            <ActionsFieldset {...actions} />
        </form>
    )
}

const Form = connect(StatelessForm);

export default (wrapper, type, run_id, pmid, state = {}) => (
    <Provider store={store(state)}>
        <Form type={type} run_id={run_id} pmid={pmid} wrapper={wrapper} />
    </Provider>
)
