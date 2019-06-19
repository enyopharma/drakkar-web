import React from 'react'
import { render } from 'react-dom'
import { createStore, applyMiddleware } from 'redux'
import { Provider, connect } from 'react-redux'
import thunk from 'redux-thunk';

import Form from './components/Form'
import reducer from './reducer'
import { mapStateToProps, mapDispatchToProps, mergeProps } from './state2props'

const init = (wrapper, container, type, run_id, pmid, state = {}) => {
    let store = createStore(reducer, state, applyMiddleware(thunk))

    const App = connect(mapStateToProps, mapDispatchToProps, mergeProps)(Form);

    render(
        <Provider store={store}>
            <App type={type} run_id={run_id} pmid={pmid} wrapper={wrapper} />
        </Provider>,
        document.getElementById(container)
    )
}

window.description = { form: init }
