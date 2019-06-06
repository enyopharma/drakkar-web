import React from 'react'

import UniprotSection from './UniprotSection'
import MappingSection from './MappingSection'
import SequenceSection from './SequenceSection'

const InteractorFieldset = ({ i, type, protein, sequence, mapping }) => {
    return (
        <fieldset>
            <legend>
                <i className={'fas fa-circle small text-' + (type == 'h' ? 'primary' : 'danger')} />
                &nbsp;
                Interactor {i}
            </legend>
            <h3>Uniprot</h3>
            <UniprotSection {...protein} />
            <h3>Sequence</h3>
            {protein.selected == null ? (
                <p>
                    Please select an uniprot entry first.
                </p>
            ) : (
                <SequenceSection {...sequence} />
            )}
            <h3>Mapping</h3>
            {protein.selected == null || sequence.editing ? (
                <p>
                    Please select a sequence first.
                </p>
            ) : (
                <MappingSection {...mapping} />
            )}
        </fieldset>
    )
}

export default InteractorFieldset
