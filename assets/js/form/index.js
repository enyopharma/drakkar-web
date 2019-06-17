import React from 'react'
import { render } from 'react-dom'
import { createStore, applyMiddleware } from 'redux'
import { Provider, connect } from 'react-redux'
import thunk from 'redux-thunk';

import Form from './components/Form'
import reducer from './reducer'
import creators from './creators'
import formatters from './formatters'

const mapStateToInteractorProps = (i, type, state) => {
    const source = formatters.source(state)
    const protein = formatters.protein(state)
    const alignment = formatters.alignment(state.alignment, protein.sequences)

    return {
        protein: {
            type: type,
            editable: ! state.processing,
            selected: state.protein,
        },
        sequence: {
            editing: state.editing,
            display: {
                valid: ! state.editing,
                source: source,
                protein: protein,
            },
            toggle: {
                editable: type == 'v' && ! state.editing && ! state.processing,
                source: source,
                protein: protein,
            },
            editor: {
                source: source,
                protein: protein,
            }
        },
        mapping: {
            selecting: state.alignment != null,
            display: {
                protein: protein,
            },
            editor: {
                query: state.qalignment,
                processing: state.processing,
                protein: protein,
            },
            modal: {
                i: i,
                type: type,
                alignment: formatters.alignment(state.alignment, protein.sequences),
            },
        }
    }
}

const mapDispatchToInteractorProps = (i, type, dispatch) => {
    return {
        protein: {
            select: protein => dispatch(creators.protein.select(i, protein)),
            unselect: () => dispatch(creators.protein.unselect(i)),
        },
        sequence: {
            toggle: {
                edit: () => dispatch(creators.mature.edit(i)),
            },
            editor: {
                update: mature => dispatch(creators.mature.update(i, mature)),
            },
        },
        mapping: {
            display: {
                remove: index => dispatch(creators.alignment.remove(i, index)),
            },
            editor: {
                update: query => dispatch(creators.alignment.update(i, query)),
                fire: sequences => dispatch(creators.alignment.fire(i, sequences)),
            },
            modal: {
                add: alignment => dispatch(creators.alignment.add(i, alignment)),
                cancel: () => dispatch(creators.alignment.cancel(i)),
            }
        }
    }
}

const mergeInteractorProps = (i, type, props, actions) => {
    return {
        i: i,
        type: type,
        protein: Object.assign(props.protein, actions.protein),
        sequence: {
            editing: props.sequence.editing,
            display: Object.assign(props.sequence.display, actions.sequence.display),
            toggle: Object.assign(props.sequence.toggle, actions.sequence.toggle),
            editor: Object.assign(props.sequence.editor, actions.sequence.editor),
        },
        mapping: {
            selecting: props.mapping.selecting,
            display: Object.assign(props.mapping.display, actions.mapping.display),
            editor: Object.assign(props.mapping.editor, actions.mapping.editor),
            modal: Object.assign(props.mapping.modal, actions.mapping.modal),
        }
    }
}

const mapStateToProps = (state, { run_id, pmid, i1type, i2type }) => {
    return {
        method: {
            selected: state.method
        },
        interactor1: mapStateToInteractorProps(1, i1type, state.interactor1),
        interactor2: mapStateToInteractorProps(2, i2type, state.interactor2),
        actions: {
            saving: state.saving,
            feedback: state.feedback,
            savable: ! state.interactor1.editing && ! state.interactor2.editing
                    && ! state.interactor1.processing && ! state.interactor2.processing
                    && state.method != null
                    && state.interactor1.protein != null
                    && state.interactor1.name != ''
                    && state.interactor1.start != ''
                    && state.interactor1.stop != ''
                    && state.interactor2.protein != null
                    && state.interactor2.name != ''
                    && state.interactor2.start != ''
                    && state.interactor2.stop != '',
            resetable: ! state.interactor1.processing && ! state.interactor2.processing,
        },
    }
}

const mapDispatchToProps = (dispatch, { run_id, pmid, i1type, i2type }) => {
    return {
        method: {
            select: method => dispatch(creators.method.select(method)),
            unselect: () => dispatch(creators.method.unselect()),
        },
        interactor1: mapDispatchToInteractorProps(1, i1type, dispatch),
        interactor2: mapDispatchToInteractorProps(2, i2type, dispatch),
        actions: {
            save: () => dispatch(creators.save(run_id, pmid)),
            reset: () => dispatch(creators.reset()),
        },
    }
}

const mergeProps = (props, actions, { run_id, pmid, i1type, i2type }) => {
    return {
        method: Object.assign(props.method, actions.method),
        interactor1: mergeInteractorProps(1, i1type, props.interactor1, actions.interactor1),
        interactor2: mergeInteractorProps(2, i2type, props.interactor2, actions.interactor2),
        actions: Object.assign(props.actions, actions.actions),
    }
}

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
