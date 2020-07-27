import React from 'react'
import { proteins as api } from '../../src/api'
import { useInteractorSelector } from '../../src/hooks'
import { InteractorI, ProteinType } from '../../src/types'

import { ProteinAlert } from './ProteinAlert'
import { ProteinSearchField } from './ProteinSearchField'

type Props = {
    i: InteractorI,
    type: ProteinType,
    accession: string,
    processing: boolean,
}

export const ProteinFieldset: React.FC<{ i: InteractorI }> = ({ i }) => {
    const { accession, ...props } = useInteractorSelector(i, state => state)

    return (
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
}

const ProteinAlertLoader: React.FC<Props> = ({ accession, ...props }) => {
    const protein = api.select(accession).read()

    return <ProteinAlert protein={protein} {...props} />
}
