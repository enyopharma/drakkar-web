import React from 'react'

import { ProteinType, Protein } from '../../src/types'

type Props = {
    protein: Protein,
    processing: boolean,
    unselect: () => void,
}

const classes: Record<ProteinType, string> = {
    'h': 'alert alert-primary',
    'v': 'alert alert-danger',
}

export const ProteinAlert: React.FC<Props> = ({ protein, processing, unselect }) => {
    return (
        <div className={classes[protein.type]}>
            <strong>{protein.accession}</strong> - {[
                protein.taxon,
                protein.name,
                protein.description,
            ].join(' - ')}
            <button type="button" className="close" onClick={e => unselect()} disabled={processing}>
                <span>&times;</span>
            </button>
        </div>
    )
}
