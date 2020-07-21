import React from 'react'

import { ProteinType } from '../../src/types'

import { ProteinAlert } from './ProteinAlert'
import { ProteinSearchField } from './ProteinSearchField'

import { proteins as api } from '../../src/api'

type Props = {
    type: ProteinType,
    accession: string | null,
    processing: boolean,
    select: (accession: string) => void,
    unselect: () => void,
}

export const ProteinFieldset: React.FC<Props> = ({ accession, ...props }) => (
    <React.Suspense fallback={null}>
        <fieldset>
            <legend>Protein</legend>
            <div className="row">
                <div className="col">
                    {accession == null
                        ? <ProteinSearchField {...props} />
                        : <ProteinAlertLoader accession={accession} {...props} />
                    }
                </div>
            </div>
        </fieldset>
    </React.Suspense>
)

const ProteinAlertLoader: React.FC<Props & { accession: string }> = ({ accession, ...props }) => {
    const protein = api.select(accession).read()

    return <ProteinAlert protein={protein} {...props} />
}
