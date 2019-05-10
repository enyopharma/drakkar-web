import qs from 'query-string'
import fetch from 'cross-fetch'
import React, { useState, useEffect } from 'react'

import SearchField from './SearchField'

const MethodFieldset = ({ method, actions }) => {
    const [q, setQ] = useState('');
    const [methods, setMethods] = useState([]);

    useEffect(() => {
        fetch('/methods?' + qs.stringify({q: q}))
            .then(response => response.json(), error => console.log(error))
            .then(json => setMethods(json.data.methods))
    }, [q])

    const format = method => [method.psimi_id, method.name].join(' - ')

    return (
        <fieldset>
            <legend>
                <i className="fas fa-circle small text-info"></i>
                &nbsp;
                Method
            </legend>
            <div className="form-group row">
                <div className="col">
                    {method == null ? (
                        <SearchField
                            value={q}
                            results={methods}
                            search={setQ}
                            select={actions.selectMethod}
                            format={format}
                        >
                            Search a method...
                        </SearchField>
                    ) : (
                        <div className="alert alert-info">
                            <strong>{method.psimi_id}</strong> - {method.name}
                            <button
                                type="button"
                                className="close"
                                onClick={actions.unselectMethod}
                            >
                                <span>&times;</span>
                            </button>
                        </div>
                    )}
                </div>
            </div>
        </fieldset>
    )
}

export default MethodFieldset
