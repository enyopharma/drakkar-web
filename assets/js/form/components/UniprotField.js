import React from 'react'

import api from '../api'
import SearchField from './SearchField'

const UniprotField = ({ type, protein, processing, select, unselect }) => {
    const searchProteins = (q, handler) => {
        api.protein.search(type, q, proteins => {
            handler(proteins.map(protein => ({
                value: protein,
                label: [
                    protein.accession,
                    protein.name,
                    protein.description,
                ].join(' - '),
            })))
        })
    }

    const selectProtein = (protein) => {
        api.protein.select(protein.accession, (protein) => {
            select(protein)
        })
    }

    return (
        <React.Fragment>
            <div style={{display: protein == null ? 'block' : 'none'}}>
                <SearchField value={protein} search={searchProteins} select={selectProtein}>
                    Search an uniprot entry...
                </SearchField>
            </div>
            {protein == null ? null : (
                <div className={'mb-0 alert alert-' + (type == 'h' ? 'primary' : 'danger')}>
                    <strong>{protein.accession}</strong> - {protein.name} - {protein.description}
                    <button
                        type="button"
                        className="close"
                        onClick={unselect}
                        disabled={processing}
                    >
                        <span>&times;</span>
                    </button>
                </div>
            )}
        </React.Fragment>
    )
}

export default UniprotField
