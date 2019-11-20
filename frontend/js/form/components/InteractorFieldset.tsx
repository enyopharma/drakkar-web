import React, { useState, useEffect } from 'react'

import { proteins as api } from '../api'
import { ProteinType, InteractorI, Protein, Mature, Sequences, Alignment } from '../types'

import { UniprotSection } from './UniprotSection'
import { MappingSection } from './MappingSection'
import { SequenceSection } from './SequenceSection'

type Props = {
    i: InteractorI,
    type: ProteinType,
    accession: string | null,
    editing: boolean,
    uniprot: {
        type: ProteinType,
        query: string,
        editable: boolean,
        update: (query: string) => void,
        select: (protein: string) => void,
        unselect: () => void,
    },
    sequence: {
        type: ProteinType,
        current: Mature,
        valid: boolean,
        editable: boolean,
        editing: boolean,
        edit: () => void,
        update: (mature: Mature) => void,
    },
    mapping: {
        i: InteractorI,
        type: ProteinType,
        name: string,
        start: number,
        stop: number,
        query: string,
        selecting: boolean,
        processing: boolean,
        alignment: Alignment,
        mapping: Alignment[],
        update: (sequence: string) => void,
        fire: (query: string, sequences: Sequences) => void,
        add: (alignment: Alignment) => void,
        remove: (i: number) => void,
        cancel: () => void,
    },
}

export const InteractorFieldset: React.FC<Props> = ({ i, type, accession, editing, uniprot, sequence, mapping }) => {
    const [protein, setProtein] = useState<Protein>(null)

    useEffect(() => {
        accession == null
            ? setProtein(null)
            : api.select(accession).then(protein => setProtein(protein))
    }, [accession, editing])

    return (
        <fieldset>
            <legend>
                <span className={'fas fa-circle small text-' + (type == 'h' ? 'primary' : 'danger')}></span>
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
