import React from 'react'

import api from '../api'
import SearchField from './SearchField'

const UniprotSection = ({ type, query, protein, editable, update, select, unselect }) => {
    const search = q => api.proteins.search(type, q).then(proteins => {
        return proteins.map(protein => ({
            value: protein.accession, label: [
                protein.accession,
                protein.name,
                protein.description,
            ].join(' - '),
        }))
    })

    return (
        <div className="row">
            <div className="col">
                <div style={{display: protein == null ? 'block' : 'none'}}>
                    <SearchField query={query} update={update} search={search} select={select}>
                        Search an uniprot entry...
                    </SearchField>
                </div>
                {protein == null ? null : (
                    <div className={'mb-0 alert alert-' + (type == 'h' ? 'primary' : 'danger')}>
                        <strong>{protein.accession}</strong> - {protein.name} - {protein.description}
                        <button
                            type="button"
                            className="close"
                            onClick={e => unselect()}
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
