import React from 'react'

import api from '../api'
import SearchField from './SearchField'

const UniprotSection = ({ type, protein, editable, select, unselect }) => {
    const searchProteins = q => api.protein.search(type, q).then(proteins => {
        return proteins.map(protein => ({
            value: protein,
            label: [
                protein.accession,
                protein.name,
                protein.description,
            ].join(' - '),
        }))
    })

    const selectProtein = protein => api.protein.select(protein.accession)
        .then(protein => select(protein))
        .catch(error => console.log(error))

    return (
        <div className="row">
            <div className="col">
                <div style={{display: protein == null ? 'block' : 'none'}}>
                    <SearchField search={searchProteins} select={selectProtein}>
                        Search an uniprot entry...
                    </SearchField>
                </div>
                {protein == null ? null : (
                    <div className={'mb-0 alert alert-' + (protein.type == 'h' ? 'primary' : 'danger')}>
                        <strong>{protein.accession}</strong> - {protein.name} - {protein.description}
                        <button
                            type="button"
                            className="close"
                            onClick={unselect}
                            disabled={! editable}
                        >
                            <span>&times;</span>
                        </button>
                    </div>
                )}
            </div>
        </div>
    )
}

export default UniprotSection
