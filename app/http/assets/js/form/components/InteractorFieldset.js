import React, { useState, useEffect } from 'react'

import api from '../api'
import UniprotSection from './UniprotSection'
import MappingSection from './MappingSection'
import SequenceSection from './SequenceSection'

const InteractorFieldset = ({ i, type, accession, editing, uniprot, sequence, mapping }) => {
    const [protein, setProtein] = useState(null)

    useEffect(() => { accession == null
        ? setProtein(null)
        : api.proteins.select(accession).then(protein => setProtein(protein))
    }, [accession, editing])

    return (
        <fieldset>
            <legend>
                <i className={'fas fa-circle small text-' + (type == 'h' ? 'primary' : 'danger')} />
                &nbsp;
                Interactor {i}
            </legend>
            <h3>Uniprot</h3>
            <UniprotSection {...uniprot} protein={protein} />
            <h3>Sequence</h3>
            {protein == null ? (
                <p>
                    Please select an uniprot entry first.
                </p>
            ) : (
                <SequenceSection {...sequence} protein={protein} />
            )}
            <h3>Mapping</h3>
            {protein == null || editing ? (
                <p>
                    Please select a sequence first.
                </p>
            ) : (
                <MappingSection {...mapping} protein={protein} />
            )}
        </fieldset>
    )
}

export default InteractorFieldset
