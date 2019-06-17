import React from 'react'

import api from '../api'
import SearchField from './SearchField'

const UniprotSection = ({ type, query, selected, editable, update, select, unselect }) => {
    const search = q => api.protein.search(type, q).then(proteins => {
        return proteins.map(protein => ({
            value: protein,
            label: [
                protein.accession,
                protein.name,
                protein.description,
            ].join(' - '),
        }))
    })

    return (
        <div className="row">
            <div className="col">
                <div style={{display: selected == null ? 'block' : 'none'}}>
                    <SearchField query={query} update={update} search={search} select={select}>
                        Search an uniprot entry...
                    </SearchField>
                </div>
                {selected == null ? null : (
                    <div className={'mb-0 alert alert-' + (type == 'h' ? 'primary' : 'danger')}>
                        <strong>{selected.accession}</strong> - {selected.name} - {selected.description}
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
