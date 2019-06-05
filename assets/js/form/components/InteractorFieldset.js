import React, { useState } from 'react'

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

    const editSequence = () => {
        setEditing(true)
    }

    const updateSequence = mature => {
        setEditing(false)
        actions.updateMature(mature)
    }

    const addAlignment = alignment => {
        actions.addAlignment(alignment)
    }

    const removeAlignment = i => {
        actions.removeAlignment(i)
    }

    // compute width of the sequence associated to the given accession.
    const width = accession => {
        if (interactor.protein != null) {
            if (accession == interactor.protein.accession) {
                return interactor.stop - interactor.start + 1
            }

            const isoforms = interactor.protein.isoforms.filter(isoform => {
                return accession == isoform.accession
            })

            if (isoforms.length == 1) {
                return isoforms.first().sequence.length
            }
        }

        throw new Error(`Invalid accession '${accession}'.`)
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
                    edit={editSequence}
                    update={updateSequence}
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
                    setProcessing={setProcessing}
                    add={addAlignment}
                    remove={removeAlignment}
                />
            )}
        </fieldset>
    )
}

export default InteractorFieldset
