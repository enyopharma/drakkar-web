import React, { useState, useEffect } from 'react'
import { InteractorI, Protein, Isoform, Coordinates, Sequences, Alignment } from '../../src/types'

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

const reduceSequences = (protein: Protein) => {
    return protein.isoforms.reduce((sequences: Sequences, isoform: Isoform) => {
        sequences[isoform.accession] = isoform.sequence
        return sequences
    }, {})
}

const matureSequences = (protein: Protein, start: number, stop: number) => {
    const sequence = protein.sequence.slice(start - 1, stop)

    return { [protein.accession]: sequence }
}

const reduceCoordinates = (protein: Protein) => {
    return protein.isoforms.reduce((reduced: Coordinates, isoform: Isoform) => {
        reduced[isoform.accession] = {
            start: 1,
            stop: isoform.sequence.length,
            length: isoform.sequence.length,
        }
        return reduced
    }, {})
}

const matureCoordinates = (protein: Protein, start: number, stop: number) => {
    const length = stop - start + 1
    return {
        [protein.accession]: { start, stop, length }
    }
}

const scaledDomains = (protein: Protein, start: number, stop: number) => {
    return protein.domains.map(domain => {
        return {
            key: domain.key,
            description: domain.description,
            start: domain.start - start + 1,
            stop: domain.stop - start + 1,
            valid: domain.start >= start && domain.stop <= stop,
        }
    })
}

export const MappingSection: React.FC<Props> = ({ protein, start, stop, alignment, ...props }) => {
    const [query, setQuery] = useState<string>('')

    useEffect(() => { if (alignment == null) setQuery('') }, [alignment])

    const isFull = start == 1 && stop == protein.sequence.length

    const sequences = isFull
        ? reduceSequences(protein)
        : matureSequences(protein, start, stop)

    const coordinates = isFull
        ? reduceCoordinates(protein)
        : matureCoordinates(protein, start, stop)

    const domains = scaledDomains(protein, start, stop)

    const canonical = sequences[protein.accession]

    return (
        <React.Fragment>
            {alignment && (
                <MappingModal
                    type={protein.type}
                    coordinates={coordinates}
                    alignment={alignment}
                    {...props}
                />
            )}
            <MappingEditor
                query={query}
                canonical={canonical}
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
