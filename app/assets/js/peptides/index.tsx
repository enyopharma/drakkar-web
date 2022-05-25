import React from 'react'
import { render } from 'react-dom'

import { PeptideType, Peptide } from './src/types'

import { PeptideList } from './components/PeptideList'

type InitPeptide = (container: string, type: PeptideType, descriptions: Peptide[]) => ReturnType<typeof render>

const peptides: InitPeptide = (container, type, peptides) => {
    render(<PeptideList type={type} peptides={peptides} />, document.getElementById(container))
}

declare global {
    interface Window {
        peptides: InitPeptide;
    }
}

window.peptides = peptides
