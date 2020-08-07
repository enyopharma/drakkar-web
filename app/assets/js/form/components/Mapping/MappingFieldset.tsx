import React from 'react'
import { proteins as api } from '../../src/api'
import { useInteractorSelector } from '../../src/hooks'
import { InteractorI, Alignment } from '../../src/types'

import { MappingSection } from './MappingSection'

type Props = {
    i: InteractorI
    protein_id: number
    name: string
    start: number
    stop: number
    mapping: Alignment[]
    processing: boolean
    alignment: Alignment | null
}

export const MappingFieldset: React.FC<{ i: InteractorI }> = ({ i }) => {
    const { protein_id, start, stop, ...props } = useInteractorSelector(i, state => state)

    return (
        <React.Suspense fallback={null}>
            <fieldset>
                <legend>Mapping</legend>
                {protein_id == null || start == null || stop == null
                    ? <p>Please select a sequence first.</p>
                    : <MappingSectionLoader protein_id={protein_id} start={start} stop={stop} {...props} />
                }
            </fieldset>
        </React.Suspense>
    )
}

const MappingSectionLoader: React.FC<Props> = ({ protein_id, ...props }) => {
    const protein = api.select(protein_id).read()

    return <MappingSection protein={protein} {...props} />
}
