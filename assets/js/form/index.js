import React from 'react'
import { render } from 'react-dom'
import { createStore, applyMiddleware } from 'redux'
import { Provider, connect } from 'react-redux'
import thunk from 'redux-thunk'

import Form from './components/Form'
import actions from './actions'
import creators from './creators'
import reducers from './reducers'

const reducer = (state, action) => {
    return {
        run_id: state.run_id,
        pmid: state.pmid,
        method: reducers.method(state.method, action),
        interactor1: reducers.interactor(1, state.interactor1, action),
        interactor2: reducers.interactor(2, state.interactor2, action),
    }
}

const mapStateToProps = state => state

const mapDispatchToProps = dispatch => {
    return {
        actions: {
            method: {
                selectMethod: (method) => dispatch(creators.selectMethod(method)),
                unselectMethod: () => dispatch(creators.unselectMethod()),
            },
            interactor1: {
                selectProtein: (protein) => dispatch(creators.selectProtein(1, protein)),
                unselectProtein: () => dispatch(creators.unselectProtein(1)),
                updateMature: (mature) => dispatch(creators.updateMature(1, mature)),
            },
            interactor2: {
                selectProtein: (protein) => dispatch(creators.selectProtein(2, protein)),
                unselectProtein: () => dispatch(creators.unselectProtein(2)),
                updateMature: (mature) => dispatch(creators.updateMature(2, mature)),
            },
        }
    }
}

const App = connect(mapStateToProps, mapDispatchToProps)(Form);

window.form = {
    create: (id, type, run_id, pmid) => {
        window.form.edit(id, type, { run_id: run_id, pmid: pmid })
    },

    edit: (id, type, state) => {
        let store = createStore(reducer, state, applyMiddleware(thunk))

        render(
            <Provider store={store}><App type={type} /></Provider>,
            document.getElementById(id)
        )
    }
}
