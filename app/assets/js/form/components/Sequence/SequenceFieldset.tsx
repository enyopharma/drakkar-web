import React from 'react'

import { InteractorI } from '../../src/types'
import { proteins as api } from '../../src/api'

import { SequenceSection } from './SequenceSection'

type Props = {
    i: InteractorI,
    accession: string | null,
    name: string,
    start: number | null,
    stop: number | null,
    editing: boolean,
    processing: boolean,
}

export const SequenceFieldset: React.FC<Props> = ({ accession, ...props }) => (
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

const SequenceSectionLoader: React.FC<Props & { accession: string }> = ({ accession, ...props }) => {
    const protein = api.select(accession).read()

    return <SequenceSection protein={protein} {...props} />
}
