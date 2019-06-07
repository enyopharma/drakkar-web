import React from 'react'
import { render } from 'react-dom'
import { createStore, applyMiddleware } from 'redux'
import { Provider, connect } from 'react-redux'
import thunk from 'redux-thunk';

import Form from './components/Form'
import actions from './actions'
import creators from './creators'
import reducer from './reducer'

const getsequence = ({ protein }) => {
    return protein == null ? '' : protein.sequence
}

const getmatureseq = ({ start, stop, protein }) => {
    if (start == '' || stop == '' || protein == null) {
        return ''
    }

    return protein.sequence.slice(start - 1, stop)
}

const getsequences = ({ start, stop, protein }) => {
    const mature = getmatureseq(({ start, stop, protein }))

    if (mature == '') return []

    const canonical = { [protein.accession]: mature }

    return start == 1 && stop == protein.sequence.length
        ? Object.assign({}, canonical, protein.isoforms)
        : canonical
}

const getfeatures = ({ start, stop, protein }) => {
    if (start == '' || stop == '' || protein == null) {
        return []
    }

    return protein.features.map(feature => {
        return {
            key: feature.key,
            description: feature.description,
            start: feature.start - start + 1,
            stop: feature.stop - start + 1,
            valid: feature.start >= start && feature.stop <= stop,
        }
    })
}

const getmature = (data) => {
    return {
        name: data.name,
        start: data.start,
        stop: data.stop,
        sequence: getmatureseq(data),
        sequences: getsequences(data),
        features: getfeatures(data),
    }
}

const formatAlignment = (alignment, sequences) => {
    if (alignment == null) return {}

    // inject the sequence of the isoforms.
    return Object.assign(alignment, {
        isoforms: alignment.isoforms.map(isoform => {
            return Object.assign(isoform, {
                sequence: sequences[isoform.accession]
            })
        })
    })
}

const formatMapping = (mapping, sequences) => {
    return mapping.map(alignment => formatAlignment(alignment, sequences))
}

const mapStateToInteractorProps = (i, type, ui, data) => {
    const sequence = getsequence(data)
    const mature = getmature(data)

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
                sequence: sequence,
                mature: mature,
            },
            toggle: {
                type: type,
                sequence: sequence,
                mature: mature,
                editable: type == 'v' && ! ui.editing && ! ui.processing,
            },
            editor: {
                sequence: sequence,
                mature: mature,
                matures: data.protein ? data.protein.matures : [],
            }
        },
        mapping: {
            selecting: ui.alignment != null,
            sequences: mature.sequences,
            display: {
                type: type,
                mapping: formatMapping(data.mapping, mature.sequences),
            },
            editor: {
                processing: ui.processing,
                mature: mature,
                features: data.protein ? data.protein.features : [],
                mapped: data.mapping.map(alignment => alignment.sequence.toUpperCase()),
            },
            modal: {
                i: i,
                type: type,
                alignment: formatAlignment(ui.alignment, mature.sequences),
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
            editor: Object.assign(props.mapping.editor, actions.mapping.editor, {
                fire: query => actions.mapping.editor.fire(query, props.mapping.sequences)
            }),
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
    }
}

const merge = (props, actions, { i1type, i2type }) => {
    return {
        method: Object.assign(props.method, actions.method),
        interactor1: mergeInteractorProps(1, i1type, props.interactor1, actions.interactor1),
        interactor2: mergeInteractorProps(2, i2type, props.interactor2, actions.interactor2),
    }
}

const App = connect(mapStateToProps, mapDispatchToProps, merge)(Form);

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
