import React from 'react'

import { ProteinType, Protein } from '../../src/types'

import { ProteinAlert } from './ProteinAlert'
import { ProteinSearchField } from './ProteinSearchField'

type Props = {
    type: ProteinType,
    protein: Protein | null,
    query: string,
    enabled: boolean,
    update: (query: string) => void,
    select: (accession: string) => void,
    unselect: () => void,
}

export const ProteinFieldset: React.FC<Props> = ({ protein, enabled, ...props }) => {
    return (
        <fieldset>
            <legend>
                Protein
            </legend>
            <div className="row">
                <div className="col">
                    {protein == null
                        ? <ProteinSearchField {...props} />
                        : <ProteinAlert {...props} protein={protein} enabled={enabled} />
                    }
                </div>
            </div>
        </fieldset>
    )
}
