import React from 'react'

import { Protein, Mature } from '../../src/types'

import { SequenceSection } from './SequenceSection'

type Props = {
    protein: Protein | null,
    name: string,
    start: number | null,
    stop: number | null,
    editing: boolean,
    processing: boolean,
    edit: () => void,
    update: (mature: Mature) => void,
}

export const SequenceFieldset: React.FC<Props> = ({ protein, ...props }) => {
    return (
        <fieldset>
            <legend>
                Sequence
            </legend>
            {protein == null
                ? <p>Please select an uniprot entry first.</p>
                : <SequenceSection {...props} protein={protein} />
            }
        </fieldset>
    )
}
