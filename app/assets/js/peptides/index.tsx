import React from 'react'
import { render } from 'react-dom'

import { Peptide } from './src/types'

import { PeptideCard } from './components/PeptideCard'

type InitPeptide = (container: string, peptides: Peptide[]) => ReturnType<typeof render>

const peptides: InitPeptide = (container, peptides) => {
    render(<PeptideCard peptides={peptides} />, document.getElementById(container))
}

declare global {
    interface Window {
        peptides: InitPeptide;
    }
}

window.peptides = peptides
