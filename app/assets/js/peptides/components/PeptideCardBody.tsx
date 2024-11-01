import React, { useState } from 'react'

import { Run, Publication, Peptide } from '../src/types'
import { PeptideForm } from './PeptideForm'

type PeptideCardBodyProps = {
    run: Run
    publication: Publication
    peptides: Peptide[]
}

export const PeptideCardBody: React.FC<PeptideCardBodyProps> = ({ run, publication, peptides, children }) => {
    const [peptide, setPeptide] = useState<Peptide | null>(peptides.length === 0 ? null : peptides[0])

    const update = (sequence: string) => {
        const filtered = peptides.filter(p => p.sequence === sequence)

        if (filtered.length !== 1) {
            throw new Error('wtf')
        }

        setPeptide(filtered[0])
    }


    if (peptide === null) return (
        <div className="card-body">
            {children}
        </div>
    )

    return (
        <div className="card-body">
            <p>
                <select className="form-control" onChange={e => update(e.target.value)} value={peptide.sequence}>
                    {peptides.map((peptide, i) => (
                        <option key={i + 1} value={peptide.sequence}>
                            {peptide.sequence}
                        </option>
                    ))}
                </select>
            </p>
            <PeptideForm key={peptide.sequence} run={run} publication={publication} peptide={peptide} />
        </div>
    )
}
