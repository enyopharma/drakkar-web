import React from 'react'
import { useAction } from '../../src/hooks'
import { unselectProtein } from '../../src/reducer'
import { InteractorI, ProteinType, Protein } from '../../src/types'

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

    const label = [protein.taxon, protein.name, protein.description].join(' - ')

    return (
        <div className={classes[protein.type]}>
            <strong>{protein.accession}</strong> - {label}
            <button type="button" className="close" onClick={e => unselect({ i })} disabled={processing}>
                <span>&times;</span>
            </button>
        </div>
    )
}
