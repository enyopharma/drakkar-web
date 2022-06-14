import React, { useState } from "react"

import { Peptide } from "../src/types"
import { PeptideCardBody } from "./PeptideCardBody"

type PeptideCardProps = {
    peptides: Peptide[]
}

export const PeptideCard: React.FC<PeptideCardProps> = ({ peptides }) => {
    const [tab, setTab] = useState<0 | 1>(0)

    const classes1 = ['nav-link', 'text-primary']
    const classes2 = ['nav-link', 'text-danger']
    const peptidesH = peptides.filter(p => p.type === 'h')
    const peptidesV = peptides.filter(p => p.type === 'v')

    if (tab === 0) classes1.push('active')
    if (tab === 1) classes2.push('active')

    return (
        <div className="card">
            <h3 className="card-header">
                Peptide info form
            </h3>
            <div className="card-header py-0">
                <ul className="nav nav-tabs nav-justified card-header-tabs">
                    <li className="nav-item">
                        <a
                            className={classes1.join(' ')}
                            onClick={e => { e.preventDefault(); setTab(0) }}
                            href="#"
                        >
                            Human
                        </a>
                    </li>
                    <li className="nav-item">
                        <a
                            className={classes2.join(' ')}
                            onClick={e => { e.preventDefault(); setTab(1) }}
                            href="#"
                        >
                            Viral
                        </a>
                    </li>
                </ul>
            </div>
            {tab === 0
                ? <PeptideCardBody key={0} peptides={peptidesH}>No human peptide</PeptideCardBody>
                : <PeptideCardBody key={1} peptides={peptidesV}>No viral peptide</PeptideCardBody>
            }
        </div>
    )
}
