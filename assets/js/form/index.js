import React from 'react'
import { render } from 'react-dom'
import { createStore, applyMiddleware } from 'redux'
import { Provider, connect } from 'react-redux'
import thunk from 'redux-thunk';

import Form from './components/Form'
import reducer from './reducer'
import creators from './creators'
import formatters from './formatters'

const mapStateToInteractorProps = (i, type, ui, data) => {
    const source = formatters.source(data)
    const protein = formatters.protein(data)
    const alignment = formatters.alignment(ui.alignment, protein.sequences)

    return {
        protein: {
            type: type,
            editable: ! ui.processing,
            selected: data.protein,
        },
        sequence: {
            editing: ui.editing,
            display: {
                valid: ! ui.editing,
                source: source,
                protein: protein,
            },
            toggle: {
                editable: type == 'v' && ! ui.editing && ! ui.processing,
                source: source,
                protein: protein,
            },
            editor: {
                source: source,
                protein: protein,
            }
        },
        mapping: {
            selecting: ui.alignment != null,
            display: {
                protein: protein,
            },
            editor: {
                processing: ui.processing,
                protein: protein,
            },
            modal: {
                i: i,
                type: type,
                alignment: formatters.alignment(ui.alignment, protein.sequences),
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
                remove: index => dispatch(creators.alignment.remove(i, index))
            },
            editor: {
                fire: (query, sequences) => dispatch(creators.alignment.fire(i, query, sequences))
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

const mapStateToProps = (state, { i1type, i2type }) => {
    return {
        method: {
            selected: state.data.method
        },
        interactor1: mapStateToInteractorProps(1, i1type, state.ui.interactor1, state.data.interactor1),
        interactor2: mapStateToInteractorProps(2, i2type, state.ui.interactor2, state.data.interactor2),
        actions: {
            saving: state.ui.saving,
            feedback: state.ui.feedback,
            savable: ! state.ui.interactor1.editing && ! state.ui.interactor2.editing
                    && ! state.ui.interactor1.processing && ! state.ui.interactor2.processing
                    && state.data.method != null
                    && state.data.interactor1.protein != null
                    && state.data.interactor1.name != ''
                    && state.data.interactor1.start != ''
                    && state.data.interactor1.stop != ''
                    && state.data.interactor2.protein != null
                    && state.data.interactor2.name != ''
                    && state.data.interactor2.start != ''
                    && state.data.interactor2.stop != '',
            resetable: ! state.ui.interactor1.processing && ! state.ui.interactor2.processing,
        },
    }
}

const mapDispatchToProps = (dispatch, { i1type, i2type }) => {
    return {
        method: {
            select: method => dispatch(creators.method.select(method)),
            unselect: () => dispatch(creators.method.unselect()),
        },
        interactor1: mapDispatchToInteractorProps(1, i1type, dispatch),
        interactor2: mapDispatchToInteractorProps(2, i2type, dispatch),
        actions: {
            save: () => dispatch(creators.save()),
            reset: () => dispatch(creators.reset()),
        },
    }
}

const mergeProps = (props, actions, { i1type, i2type }) => {
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
        window.form.edit(id, type, {
            run_id: run_id,
            pmid: pmid,
        })
    },

    edit: (id, type, data) => {
        const i1type = 'h'
        const i2type = type == 'hh' ? 'h' : 'v'

        let store = createStore(reducer, { data: data }, applyMiddleware(thunk))

        render(
            <Provider store={store}><App i1type={i1type} i2type={i2type} /></Provider>,
            document.getElementById(id)
        )
    }
}
