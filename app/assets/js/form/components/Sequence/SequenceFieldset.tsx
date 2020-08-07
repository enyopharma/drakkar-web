import React from 'react'
import { InteractorI } from '../../src/types'
import { proteins as api } from '../../src/api'
import { useInteractorSelector } from '../../src/hooks'

import { SequenceSection } from './SequenceSection'

type Props = {
    i: InteractorI
    protein_id: number
    name: string
    start: number | null
    stop: number | null
    editing: boolean
    processing: boolean
}

export const SequenceFieldset: React.FC<{ i: InteractorI }> = ({ i }) => {
    const { protein_id, ...props } = useInteractorSelector(i, state => state)

    return (
        <React.Suspense fallback={null}>
            <fieldset>
                <legend>Sequence</legend>
                {protein_id == null
                    ? <p>Please select an uniprot entry first.</p>
                    : <SequenceSectionLoader protein_id={protein_id} {...props} />
                }
            </fieldset>
        </React.Suspense>
    )
}

const SequenceSectionLoader: React.FC<Props> = ({ protein_id, ...props }) => {
    const protein = api.select(protein_id).read()

    return <SequenceSection protein={protein} {...props} />
}
