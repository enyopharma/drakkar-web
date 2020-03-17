import React from 'react'

import { InteractorI, Protein, Sequences, Alignment } from '../../src/types'

import { MappingSection } from './MappingSection'

type Props = {
    i: InteractorI,
    protein: Protein | null,
    name: string,
    start: number | null,
    stop: number | null,
    mapping: Alignment[],
    processing: boolean,
    alignment: Alignment | null,
    fire: (query: string, sequences: Sequences) => void,
    add: (alignment: Alignment) => void,
    remove: (i: number) => void,
    cancel: () => void,
}

export const MappingFieldset: React.FC<Props> = ({ protein, start, stop, ...props }) => {
    return (
        <fieldset>
            <legend>Mapping</legend>
            {protein == null || start == null || stop == null
                ? <p>Please select a sequence first.</p>
                : <MappingSection {...props} protein={protein} start={start} stop={stop} />
            }
        </fieldset>
    )
}
