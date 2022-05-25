import React from 'react'

import { PeptideType, Peptide } from '../src/types'

type PeptideListProps = {
    type: PeptideType
    peptides: Peptide[]
}

export const PeptideList: React.FC<PeptideListProps> = ({ type, peptides }) => {
    const filtered = peptides.filter(p => p.type === type)

    return (
        <div className="card">
            <h3 className="card-header">
                {type === 'h' ? 'Human peptides' : 'Viral peptides'}
            </h3>
            {filtered.length === 0 ? (
                <div className="card-body">
                    {type === 'h' ? 'No human peptides' : 'No viral peptides'}
                </div>
            ) : (
                <ul className="list-group list-group-flush">
                    {filtered.map((peptide, i) => (
                        <li key={i} className="list-group-item">
                            {peptide.sequence}
                        </li>
                    ))}
                </ul>
            )}
        </div>
    )
}
