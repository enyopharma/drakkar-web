import React, { useState, useEffect } from 'react'

import { proteins as api } from '../api'
import { ProteinType, InteractorI, Protein, Mature, Sequences, Alignment } from '../types'

import { ProteinAlert } from './ProteinAlert'
import { SearchField } from './Shared/SearchField'
import { MappingSection } from './Mapping/MappingSection'
import { SequenceSection } from './Sequence/SequenceSection'

type Props = {
    i: InteractorI,
    type: ProteinType,
    query: string,
    accession: string,
    name: string,
    start: number,
    stop: number,
    mapping: Alignment[],
    editing: boolean,
    processing: boolean,
    alignment: Alignment,
    actions: {
        protein: {
            update: (query: string) => void,
            select: (protein: string) => void,
            unselect: () => void,
        },
        sequence: {
            edit: () => void,
            update: (mature: Mature) => void,
        },
        mapping: {
            fire: (query: string, sequences: Sequences) => void,
            add: (alignment: Alignment) => void,
            remove: (index: number) => void,
            cancel: () => void,
        }
    }
}

export const InteractorFieldset: React.FC<Props> = ({ accession, ...props }) => {
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
            <div className="row">
                <div className="col">
                    {protein == null
                        ? <SearchField {...props} {...actions.protein} search={search} placeholder="Search an uniprot entry..." />
                        : <ProteinAlert {...props} {...actions.protein} protein={protein} enabled={enabled} />
                    }
                </div>
            </div>
            <h3>Sequence</h3>
            {protein == null
                ? <p>Please select an uniprot entry first.</p>
                : <SequenceSection {...props} {...actions.sequence} protein={protein} />
            }
            <h3>Mapping</h3>
            {protein == null && !editing
                ? <p>Please select a sequence first.</p>
                : <MappingSection {...props} {...actions.mapping} protein={protein} />
            }
        </fieldset>
    )
}
