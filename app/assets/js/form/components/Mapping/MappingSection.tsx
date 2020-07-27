import React, { useState, useEffect } from 'react'

import { InteractorI, Protein, Isoform, ScaledDomain, Coordinates, Sequences, Alignment } from '../../src/types'

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
    alignment: Alignment | null,
}

export const MappingSection: React.FC<Props> = ({ protein, alignment, ...props }) => {
    const [query, setQuery] = useState<string>('')

    useEffect(() => { if (alignment == null) setQuery('') }, [alignment])

    const isFull = props.start == 1 && props.stop == protein.sequence.length

    const sequence = protein.sequence.slice(props.start - 1, props.stop)

    const sequences: Sequences = isFull
        ? protein.isoforms.reduce((sequences: Sequences, isoform: Isoform) => {
            sequences[isoform.accession] = isoform.sequence
            return sequences
        }, {})
        : { [protein.accession]: sequence }

    const coordinates: Coordinates = isFull
        ? protein.isoforms.reduce((reduced: Coordinates, isoform: Isoform) => {
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

    return (
        <React.Fragment>
            {alignment == null ? null : (
                <MappingModal
                    type={protein.type}
                    coordinates={coordinates}
                    alignment={alignment}
                    {...props}
                />
            )}
            <MappingEditor
                query={query}
                sequence={sequence}
                sequences={sequences}
                domains={domains}
                update={setQuery}
                {...props}
            />
            <MappingDisplay
                type={protein.type}
                coordinates={coordinates}
                {...props}
            />
        </React.Fragment>
    )
}
