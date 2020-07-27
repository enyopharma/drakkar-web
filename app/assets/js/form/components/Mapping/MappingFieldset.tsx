import React from 'react'
import { proteins as api } from '../../src/api'
import { InteractorI, Alignment } from '../../src/types'

import { MappingSection } from './MappingSection'
import { useInteractorSelector } from '../../src/hooks'

type Props = {
    i: InteractorI,
    accession: string,
    name: string,
    start: number,
    stop: number,
    mapping: Alignment[],
    processing: boolean,
    alignment: Alignment | null,
}

export const MappingFieldset: React.FC<{ i: InteractorI }> = ({ i }) => {
    const { accession, start, stop, ...props } = useInteractorSelector(i, state => state)

    return (
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
}

const MappingSectionLoader: React.FC<Props> = ({ accession, ...props }) => {
    const protein = api.select(accession).read()

    return <MappingSection protein={protein} {...props} />
}
