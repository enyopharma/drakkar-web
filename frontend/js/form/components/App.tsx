import React from 'react'
import { Provider } from 'react-redux'

import { create } from '../src/store'
import { connect } from '../src/props'

import { AppState } from '../src/state'
import { AppProps } from '../src/props'
import { DescriptionType } from '../src/types'

import { Form } from './Form'

const StatelessApp: React.FC<AppProps> = (props) => {
    return <Form {...props} />
}

const StatefulApp = connect(StatelessApp);

export const App = (type: DescriptionType, run_id: number, pmid: number, state: AppState) => (
    <Provider store={create(state)}>
        <StatefulApp type={type} run_id={run_id} pmid={pmid} />
    </Provider>
)
