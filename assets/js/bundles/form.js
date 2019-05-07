import React from 'react'
import { render } from 'react-dom'
import { createStore, applyMiddleware } from 'redux'
import { Provider, connect } from 'react-redux'
import qs from 'query-string'
import thunk from 'redux-thunk'
import fetch from 'cross-fetch'

import Form from './form/Form'

let n = 0

const SELECT_METHOD = n++
const UNSELECT_METHOD = n++
const SELECT_PROTEIN = n++
const UNSELECT_PROTEIN = n++
const UPDATE_MATURE = n++

const searchMethod = (q, update) => (dispatch) => {
    fetch('/methods?' + qs.stringify({q: q}))
        .then(response => response.json(), error => console.log(error))
        .then(json => update(json.data.methods))
}

const selectMethod = (method) => {
    return {
        type: SELECT_METHOD,
        method: method,
    }
}

const unselectMethod = () => {
    return {
        type: UNSELECT_METHOD,
    }
}

const searchProtein = (i, type, q, update) => (dispatch) => {
    fetch('/proteins?' + qs.stringify({type: type, q: q}))
        .then(response => response.json(), error => console.log(error))
        .then(json => update(json.data.proteins))
}

const selectProtein = (i, protein) => (dispatch) => {
    fetch('/proteins/' + protein.accession)
        .then(response => response.json(), error => console.log(error))
        .then(json => dispatch({
            i: i,
            type: SELECT_PROTEIN,
            protein: json.data.protein,
        }))
}

const unselectProtein = (i) => {
    return {
        i: i,
        type: UNSELECT_PROTEIN,
    }
}

const updateMature = (i, mature) => {
    return {
        i: i,
        type: UPDATE_MATURE,
        mature: mature,
    }
}

const mapStateToProps = state => state

const mapDispatchToProps = dispatch => {
    return {
        actions: {
            method: {
                searchMethod: (q, update) => dispatch(searchMethod(q, update)),
                selectMethod: (method) => dispatch(selectMethod(method)),
                unselectMethod: () => dispatch(unselectMethod()),
            },
            interactor1: {
                searchProtein: (type, q, update) => dispatch(searchProtein(1, type, q, update)),
                selectProtein: (protein) => dispatch(selectProtein(1, protein)),
                unselectProtein: () => dispatch(unselectProtein(1)),
                updateMature: (mature) => dispatch(updateMature(1, mature)),
            },
            interactor2: {
                searchProtein: (type, q, update) => dispatch(searchProtein(2, type, q, update)),
                selectProtein: (protein) => dispatch(selectProtein(2, protein)),
                unselectProtein: () => dispatch(unselectProtein(2)),
                updateMature: (mature) => dispatch(updateMature(2, mature)),
            },
        }
    }
}

const App = connect(mapStateToProps, mapDispatchToProps)(Form);

const reducers = {
    description: (state, action) => {
        return {
            run_id: state.run_id,
            pmid: state.pmid,
            method: reducers.method(state.method, action),
            interactor1: reducers.interactor(1, state.interactor1, action),
            interactor2: reducers.interactor(2, state.interactor2, action),
        }
    },

    method: (state = null, action) => {
        switch (action.type) {
            case SELECT_METHOD:
                return action.method
            case UNSELECT_METHOD:
                return null
            default:
                return state
        }
    },

    interactor: (i, state = {}, action) => {
        if (action.i == i) {
            return {
                protein: reducers.protein(state.protein, action),
                name: reducers.name(state.name, action),
                start: reducers.start(state.start, action),
                stop: reducers.stop(state.stop, action),
                mapping: reducers.mapping(state.mapping, action),
            }
        }
        return state
    },

    protein: (state = null, action) => {
        switch (action.type) {
            case SELECT_PROTEIN:
                return action.protein
            case UNSELECT_PROTEIN:
                return null
            default:
                return state
        }
    },

    name: (state = '', action) => {
        switch (action.type) {
            case SELECT_PROTEIN:
                return action.protein.name
            case UPDATE_MATURE:
                return action.mature.name
            case UNSELECT_PROTEIN:
                return ''
            default:
                return state
        }
    },

    start: (state = 0, action) => {
        switch (action.type) {
            case SELECT_PROTEIN:
                return 1
            case UNSELECT_PROTEIN:
                return 0
            case UPDATE_MATURE:
                return action.mature.start
            default:
                return state
        }
    },

    stop: (state = 0, action) => {
        switch (action.type) {
            case SELECT_PROTEIN:
                return action.protein.sequence.length
            case UNSELECT_PROTEIN:
                return 0
            case UPDATE_MATURE:
                return action.mature.stop
            default:
                return state
        }
    },

    mapping: (state = [], action) => {
        switch (action.type) {
            case SELECT_PROTEIN:
                return []
            case UNSELECT_PROTEIN:
                return []
            default:
                return state
        }
    },
}

window.form = {
    create: (id, type, run_id, pmid) => {
        window.form.edit(id, type, { run_id: run_id, pmid: pmid })
    },

    edit: (id, type, state) => {
        let store = createStore(reducers.description, state, applyMiddleware(thunk))

        render(
            <Provider store={store}><App type={type} /></Provider>,
            document.getElementById(id)
        )
    }
}
