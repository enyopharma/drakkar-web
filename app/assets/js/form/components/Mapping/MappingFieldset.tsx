import React from 'react'

import { InteractorI, Sequences, Alignment } from '../../src/types'

import { MappingSection } from './MappingSection'

import { proteins as api } from '../../src/api'

type Props = {
    i: InteractorI,
    accession: string | null,
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

export const MappingFieldset: React.FC<Props> = ({ accession, start, stop, ...props }) => (
    <React.Suspense fallback={null}>
        <fieldset>
            <legend>Mapping</legend>
            {accession == null || start == null || stop == null
                ? <p>Please select a sequence first.</p>
                : <MappingSectionLoader accession={accession} start={start} stop={stop} {...props} />
            }
        </fieldset>
    </React.Suspense>
)

const MappingSectionLoader: React.FC<Props & { accession: string, start: number, stop: number }> = ({ accession, ...props }) => {
    const protein = api.select(accession).read()

    return <MappingSection protein={protein} {...props} />
}
