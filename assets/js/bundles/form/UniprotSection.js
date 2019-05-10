import qs from 'query-string'
import fetch from 'cross-fetch'
import React, { useState, useEffect } from 'react'

import SearchField from './SearchField'

const UniprotSection = ({ type, protein, processing, select, unselect }) => {
    const [q, setQ] = useState('');
    const [proteins, setProteins] = useState([]);

    useEffect(() => {
        fetch('/proteins?' + qs.stringify({type: type, q: q}))
            .then(response => response.json(), error => console.log(error))
            .then(json => setProteins(json.data.proteins))
    }, [q])

    const format = protein => [
        protein.accession,
        protein.name,
        protein.description,
    ].join(' - ')

    return (
        <React.Fragment>
            <h4>Uniprot entry</h4>
            <div className="row">
                <div className="col">
                    {protein == null ? (
                        <SearchField
                            value={q}
                            results={proteins}
                            search={setQ}
                            select={select}
                            format={format}
                        >
                            Search an uniprot entry...
                        </SearchField>
                    ) : (
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
