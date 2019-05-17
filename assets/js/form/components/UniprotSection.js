import qs from 'query-string'
import fetch from 'cross-fetch'
import React, { useState, useEffect } from 'react'

import SearchField from './SearchField'

const UniprotSection = ({ type, protein, processing, select, unselect }) => {
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
            <h4>Uniprot entry</h4>
            <div className="row">
                <div className="col">
                    <SearchField
                        search={search}
                        select={select}
                        display={protein == null}
                    >
                        Search an uniprot entry...
                    </SearchField>
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
                </div>
            </div>
        </React.Fragment>
    )
}

export default UniprotSection
