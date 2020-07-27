import React from 'react'
import { InteractorI } from '../../src/types'
import { proteins as api } from '../../src/api'
import { useInteractorSelector } from '../../src/hooks'

import { SequenceSection } from './SequenceSection'

type Props = {
    i: InteractorI,
    accession: string,
    name: string,
    start: number | null,
    stop: number | null,
    editing: boolean,
    processing: boolean,
}

export const SequenceFieldset: React.FC<{ i: InteractorI }> = ({ i }) => {
    const { accession, ...props } = useInteractorSelector(i, state => state)

    return (
        <React.Suspense fallback={null}>
            <fieldset>
                <legend>Sequence</legend>
                {accession == null
                    ? <p>Please select an uniprot entry first.</p>
                    : <SequenceSectionLoader accession={accession} {...props} />
                }
            </fieldset>
        </React.Suspense>
    )
}

const SequenceSectionLoader: React.FC<Props> = ({ accession, ...props }) => {
    const protein = api.select(accession).read()

    return <SequenceSection protein={protein} {...props} />
}
