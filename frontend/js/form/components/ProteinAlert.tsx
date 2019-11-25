import React from 'react'

import { Protein } from '../src/types'

type Props = {
    protein: Protein,
    enabled: boolean,
    unselect: () => void,
}

export const ProteinAlert: React.FC<Props> = ({ protein, enabled, unselect }) => {
    return (
        <div className={'alert alert-' + (protein.type == 'h' ? 'primary' : 'danger')}>
            <strong>{protein.accession}</strong> - {[
                protein.taxon,
                protein.name,
                protein.description,
            ].join(' - ')}
            <button type="button" className="close" onClick={e => unselect()} disabled={!enabled}>
                <span>&times;</span>
            </button>
        </div>
    )
}
