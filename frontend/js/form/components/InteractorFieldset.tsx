import React, { useState, useEffect } from 'react'

import { proteins as api } from '../api'
import { InteractorProps } from '../store/connect'

import { Protein } from '../types'

import { ProteinAlert } from './ProteinAlert'
import { SearchField } from './Shared/SearchField'
import { MappingSection } from './Mapping/MappingSection'
import { SequenceSection } from './Sequence/SequenceSection'

export const InteractorFieldset: React.FC<InteractorProps> = ({ accession, ...props }) => {
    const [protein, setProtein] = useState<Protein>(null)

    useEffect(() => { api.select(accession).then(p => setProtein(p)) }, [accession])

    const search = api.search(props.type)
    const enabled = !props.processing
    const editing = props.editing
    const actions = props.actions

    return (
        <fieldset>
            <legend>
                <span className={'fas fa-circle small text-' + (props.type == 'h' ? 'primary' : 'danger')}></span>
                &nbsp;
                Interactor {props.i}
            </legend>
            <h3>Uniprot</h3>
            {protein == null
                ? <SearchField {...props} {...actions.protein} search={search} placeholder="Search an uniprot entry..." />
                : <ProteinAlert {...props} {...actions.protein} protein={protein} enabled={enabled} />
            }
            <h3>Sequence</h3>
            {protein == null
                ? <p>Please select an uniprot entry first.</p>
                : <SequenceSection {...props} {...actions.sequence} protein={protein} />
            }
            <h3>Mapping</h3>
            {protein == null || editing
                ? <p>Please select a sequence first.</p>
                : <MappingSection {...props} {...actions.mapping} protein={protein} />
            }
        </fieldset>
    )
}
