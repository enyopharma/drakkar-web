import React from 'react'

import { InteractorI, Alignment } from '../../src/types'
import { proteins as api } from '../../src/api'

import { MappingSection } from './MappingSection'

type Props = {
    i: InteractorI,
    accession: string | null,
    name: string,
    start: number | null,
    stop: number | null,
    mapping: Alignment[],
    processing: boolean,
    alignment: Alignment | null,
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
