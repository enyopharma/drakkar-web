import React from 'react'
import { render } from 'react-dom'

import { Run, Publication, Peptide } from './src/types'

import { PeptideCard } from './components/PeptideCard'

type InitPeptide = (container: string, run: Run, publication: Publication, peptides: Peptide[]) => ReturnType<typeof render>

const peptides: InitPeptide = (container, run, publication, peptides) => {
    render(<PeptideCard run={run} publication={publication} peptides={peptides} />, document.getElementById(container))
}

declare global {
    interface Window {
        peptides: InitPeptide;
    }
}

window.peptides = peptides
