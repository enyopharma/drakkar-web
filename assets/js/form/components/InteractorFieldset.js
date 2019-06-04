import React, { useState } from 'react'

import api from '../api'
import UniprotSection from './UniprotSection'
import MappingSection from './MappingSection'
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
            <h3>Uniprot</h3>
            <UniprotSection
                type={type}
                protein={interactor.protein}
                editable={! processing}
                select={selectProtein}
                unselect={unselectProtein}
            />
            <h3>Sequence</h3>
            {interactor.protein == null ? (
                <p>
                    Please select an uniprot entry first.
                </p>
            ) : (
                <SequenceSection
                    name={interactor.name}
                    start={interactor.start}
                    stop={interactor.stop}
                    protein={interactor.protein}
                    editing={editing}
                    editable={type == 'v' && ! editing && ! processing}
                    edit={startEditing}
                    update={updateMature}
                />
            )}
            <h3>Mapping</h3>
            {interactor.protein == null || editing ? (
                <p>
                    Please select a sequence first.
                </p>
            ) : (
                <MappingSection
                    start={interactor.start}
                    stop={interactor.stop}
                    protein={interactor.protein}
                    mapping={interactor.mapping}
                    processing={processing}
                    fire={fireAlignment}
                    remove={removeMapping}
                />
            )}
        </fieldset>
    )
}

export default InteractorFieldset
