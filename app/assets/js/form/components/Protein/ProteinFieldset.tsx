import React from 'react'
import { proteins as api } from '../../src/api'
import { useInteractorSelector } from '../../src/hooks'
import { InteractorI, ProteinType } from '../../src/types'

import { ProteinAlert } from './ProteinAlert'
import { ProteinSearchField } from './ProteinSearchField'

type Props = {
    i: InteractorI
    type: ProteinType
    protein_id: number
    processing: boolean
}

export const ProteinFieldset: React.FC<{ i: InteractorI }> = ({ i }) => {
    const { protein_id, ...props } = useInteractorSelector(i, state => state)

    return (
        <React.Suspense fallback={null}>
            <fieldset>
                <legend>Protein</legend>
                <div className="row">
                    <div className="col">
                        {protein_id == null
                            ? <ProteinSearchField {...props} />
                            : <ProteinAlertLoader protein_id={protein_id} {...props} />
                        }
                    </div>
                </div>
            </fieldset>
        </React.Suspense>
    )
}

const ProteinAlertLoader: React.FC<Props> = ({ protein_id, ...props }) => {
    const protein = api.select(protein_id).read()

    return <ProteinAlert protein={protein} {...props} />
}
