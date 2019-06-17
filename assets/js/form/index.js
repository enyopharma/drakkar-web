import React from 'react'
import { render } from 'react-dom'
import { createStore, applyMiddleware } from 'redux'
import { Provider, connect } from 'react-redux'
import thunk from 'redux-thunk';

import Form from './components/Form'
import reducer from './reducer'
import { mapStateToProps, mapDispatchToProps, mergeProps } from './state2props'

const App = connect(mapStateToProps, mapDispatchToProps, mergeProps)(Form);

window.form = {
    create: (id, type, run_id, pmid) => {
        window.form.edit(id, type, run_id, pmid)
    },

    edit: (id, type, run_id, pmid, data = {}) => {
        const i1type = 'h'
        const i2type = type == 'hh' ? 'h' : 'v'

        const state = { data: data }

        let store = createStore(reducer, state, applyMiddleware(thunk))

        render(
            <Provider store={store}>
                <App
                    run_id={run_id}
                    pmid={pmid}
                    i1type={i1type}
                    i2type={i2type}
                />
            </Provider>,
            document.getElementById(id)
        )
    }
}
