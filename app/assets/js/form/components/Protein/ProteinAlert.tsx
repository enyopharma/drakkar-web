import React from 'react'
import { proteins } from '../../src/api'
import { useAction } from '../../src/hooks'
import { unselectProtein } from '../../src/reducer'
import { InteractorI, ProteinType, Protein } from '../../src/types'

type Props = {
    i: InteractorI
    protein: Protein
    processing: boolean
}

const classes: Record<ProteinType, string> = {
    'h': 'alert alert-primary',
    'v': 'alert alert-danger',
}

export const ProteinAlert: React.FC<Props> = ({ i, protein, processing }) => {
    const unselect = useAction(unselectProtein)

    const label = [protein.version, protein.taxon, protein.name, protein.description].join(' - ')

    return (
        <React.Fragment>
            {protein.obsolete && <ObsoleteWarningAlert protein={protein} />}
            <div className={classes[protein.type]}>
                <strong>{protein.accession}</strong> - {label}
                <button type="button" className="close" onClick={e => unselect({ i })} disabled={processing}>
                    <span>&times;</span>
                </button>
            </div>
        </React.Fragment>
    )
}

const ObsoleteWarningAlert: React.FC<{ protein: Protein }> = ({ protein }) => (
    <div className="alert alert-warning">
        <strong>{protein.accession}</strong> - {protein.version} is now obsolete!
        Please select an up to date protein below:
    </div>
)
