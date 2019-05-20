import qs from 'query-string'
import fetch from 'cross-fetch'
import React from 'react'

import SearchField from './SearchField'

const UniprotField = ({ type, protein, processing, select, unselect }) => {
    const search = q => fetch('/proteins?' + qs.stringify({type: type, q: q}))
        .then(response => response.json(), error => console.log(error))
        .then(json => json.data.proteins.map(protein => ({
            value: protein,
            label: [
                protein.accession,
                protein.name,
                protein.description,
            ].join(' - '),
        })))

    return (
        <React.Fragment>
            <div style={{display: protein == null ? 'block' : 'none'}}>
                <SearchField value={protein} search={search} select={select}>
                    Search an uniprot entry...
                </SearchField>
            </div>
            {protein == null ? null : (
                <div className={'alert alert-' + (type == 'h' ? 'primary' : 'danger')}>
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
