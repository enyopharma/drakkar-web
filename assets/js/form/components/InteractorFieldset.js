import React, { useState } from 'react'

import api from '../api'
import Mapping from './Mapping'
import UniprotField from './UniprotField'
import MatureProtein from './MatureProtein'
import SequenceSection from './SequenceSection'

const InteractorFieldset = ({ i, type, interactor, actions }) => {
    const [editing, setEditing] = useState(type == 'v')
    const [processing, setProcessing] = useState(false)

    const selectProtein = (protein) => {
        setEditing(type == 'v')
        actions.selectProtein(protein)
    }

    const unselectProtein = () => {
        actions.unselectProtein()
    }

    const startEditing = () => {
        setEditing(true)
    }

    const updateMature = mature => {
        setEditing(false)
        actions.updateMature(mature)
    }

    const fireAlignment = (query, subjects) => {
        setProcessing(true)

        api.alignment(query, subjects, (alignment) => {
            actions.addAlignment(alignment)
            setProcessing(false)
        })
    }

    const removeMapping = (...idxs) => {
        actions.removeMapping(...idxs)
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
                        editable={! processing}
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
                        valid={! editing}
                        editable={type == 'v' && ! editing && ! processing}
                        edit={startEditing}
                    />
                    {! editing ? null : (
                        <MatureProtein
                            name={interactor.name}
                            start={interactor.start}
                            stop={interactor.stop}
                            protein={interactor.protein}
                            update={updateMature}
                        />
                    )}
                    <h4>Mapping</h4>
                    {editing ? (
                        <p>
                            Please select a sequence first.
                        </p>
                    ) : (
                        <Mapping
                            start={interactor.start}
                            stop={interactor.stop}
                            protein={interactor.protein}
                            mapping={interactor.mapping}
                            processing={processing}
                            fire={fireAlignment}
                            remove={removeMapping}
                        />
                    )}
                </React.Fragment>
            )}
        </fieldset>
    )
}

export default InteractorFieldset
