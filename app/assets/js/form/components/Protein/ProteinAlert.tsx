import React from 'react'
import { useAction } from '../../src/hooks'

import { InteractorI, ProteinType, Protein } from '../../src/types'
import { unselectProtein } from '../../src/reducer'

type Props = {
    i: InteractorI,
    protein: Protein,
    processing: boolean,
}

const classes: Record<ProteinType, string> = {
    'h': 'alert alert-primary',
    'v': 'alert alert-danger',
}

export const ProteinAlert: React.FC<Props> = ({ i, protein, processing }) => {
    const unselect = useAction(unselectProtein)

    return (
        <div className={classes[protein.type]}>
            <strong>{protein.accession}</strong> - {[
                protein.taxon,
                protein.name,
                protein.description,
            ].join(' - ')}
            <button type="button" className="close" onClick={e => unselect({ i })} disabled={processing}>
                <span>&times;</span>
            </button>
        </div>
    )
}
