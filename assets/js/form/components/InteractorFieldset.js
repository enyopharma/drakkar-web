import React, { useReducer } from 'react'

import Mapping from './Mapping'
import UniprotField from './UniprotField'
import MappingEditor from './MappingEditor'
import SequenceSection from './SequenceSection'
import MatureProteinEditor from './MatureProteinEditor'

const reducer = (state, action) => {
    switch (action.type) {
        case 'set.editing':
            return { editing: action.value, processing: false }
        break
        case 'start.editing':
            return { editing: true, processing: false }
        break
        case 'stop.editing':
            return { editing: false, processing: false }
        break

        case 'start.processing':
            return { editing: false, processing: true }
        break
        case 'stop.processing':
            return { editing: false, processing: false }
        break
        default:
            throw new Error(`InteractorFieldset: invalid state ${action.type}.`)
    }
}

const InteractorFieldset = ({ i, type, interactor, actions }) => {
    const [state, dispatch] = useReducer(reducer, {
        editing: type == 'v',
        processing: false,
    })

    const setEditing = (value) => dispatch({type: 'set.editing', value: value})
    const startEditing = () => dispatch({type: 'start.editing'})
    const stopEditing = () => dispatch({type: 'stop.editing'})
    const startProcessing = () => dispatch({type: 'start.processing'})
    const stopProcessing = () => dispatch({type: 'stop.processing'})

    const selectProtein = (protein) => {
        actions.selectProtein(protein)
    }

    const unselectProtein = () => {
        setEditing(type == 'v')
        actions.unselectProtein()
    }

    const updateMature = mature => {
        stopEditing()
        actions.updateMature(mature)
    }

    const fireAlignment = (sequence, subjects) => {
        startProcessing()
        setTimeout(() => {
            stopProcessing()
            actions.addAlignment({sequence: sequence})
        }, 5000)
    }

    const removeAlignment = i => {
        actions.removeAlignment(i)
    }

    return (
        <fieldset>
            <legend>
                <i className={'fas fa-circle small text-' + (type == 'h' ? 'primary' : 'danger')} />
                &nbsp;
                Interactor {i}
            </legend>
            <div className="row">
                <div className="col">
                    <UniprotField
                        type={type}
                        protein={interactor.protein}
                        processing={state.processing}
                        select={selectProtein}
                        unselect={unselectProtein}
                    />
                </div>
            </div>
            {interactor.protein == null ? null : (
                <React.Fragment>
                    <h4>Sequence</h4>
                    <SequenceSection
                        name={interactor.name}
                        start={interactor.start}
                        stop={interactor.stop}
                        protein={interactor.protein}
                        editing={state.editing}
                        processing={state.processing}
                        edit={startEditing}
                    />
                    {interactor.protein.type == 'h' || ! state.editing ? null : (
                        <MatureProteinEditor
                            name={interactor.name}
                            start={interactor.start}
                            stop={interactor.stop}
                            protein={interactor.protein}
                            update={updateMature}
                        />
                    )}
                    <h4>Mapping</h4>
                    {state.editing ? (
                        <p>
                            Please select a sequence first.
                        </p>
                    ) : (
                        <React.Fragment>
                            <MappingEditor
                                start={interactor.start}
                                stop={interactor.stop}
                                protein={interactor.protein}
                                mapping={interactor.mapping}
                                processing={state.processing}
                                fire={fireAlignment}
                            />
                            <Mapping
                                start={interactor.start}
                                stop={interactor.stop}
                                protein={interactor.protein}
                                mapping={interactor.mapping}
                                removeAlignment={removeAlignment}
                            />
                        </React.Fragment>
                    )}
                </React.Fragment>
            )}
        </fieldset>
    )
}

export default InteractorFieldset
