import React from 'react'

import { Coordinates, Sequences, Alignment } from '../../types'
import { InteractorI, ProteinType, Protein, ScaledDomain } from '../../types'

import { MappingModal } from './MappingModal'
import { MappingEditor } from './MappingEditor'
import { MappingDisplay } from './MappingDisplay'

type Props = {
    i: InteractorI,
    protein: Protein,
    name: string,
    start: number,
    stop: number,
    mapping: Alignment[],
    processing: boolean,
    alignment: Alignment,
    fire: (query: string, sequences: Sequences) => void,
    add: (alignment: Alignment) => void,
    remove: (i: number) => void,
    cancel: () => void,
}

export const MappingSection: React.FC<Props> = ({ protein, ...props }) => {
    const isFull = props.start == 1 && props.stop == protein.sequence.length

    const sequence = protein.sequence.slice(props.start - 1, props.stop)

    const sequences: Sequences = isFull
        ? protein.isoforms.reduce((sequences, isoform) => {
            sequences[isoform.accession] = isoform.sequence
            return sequences
        }, {})
        : { [protein.accession]: sequence }

    const coordinates: Coordinates = isFull
        ? protein.isoforms.reduce((reduced, isoform) => {
            reduced[isoform.accession] = {
                start: 1,
                stop: isoform.sequence.length,
                length: isoform.sequence.length,
            }
            return reduced
        }, {})
        : {
            [protein.accession]: {
                start: props.start,
                stop: props.stop,
                length: props.stop - props.start + 1
            }
        }

    const domains: ScaledDomain[] = protein.domains.map(domain => {
        return {
            key: domain.key,
            description: domain.description,
            start: domain.start - props.start + 1,
            stop: domain.stop - props.start + 1,
            valid: domain.start >= props.start && domain.stop <= props.stop,
        }
    })

    const type = protein.type
    const fire = (query: string) => props.fire(query, sequences)

    return (
        <React.Fragment>
            <MappingEditor {...props} sequence={sequence} domains={domains} fire={fire} />
            <MappingDisplay {...props} type={type} coordinates={coordinates} />
            {props.alignment == null ? null : (
                <MappingModal {...props} type={type} coordinates={coordinates} />
            )}
        </React.Fragment>
    )
}
