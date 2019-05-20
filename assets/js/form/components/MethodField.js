import qs from 'query-string'
import fetch from 'cross-fetch'
import React from 'react'

import SearchField from './SearchField'

const MethodField = ({ method, select, unselect }) => {
    const search = q => fetch('/methods?' + qs.stringify({q: q}))
        .then(response => response.json(), error => console.log(error))
        .then(json => json.data.methods.map(method => ({
            value: method,
            label: [method.psimi_id, method.name].join(' - '),
        })))

    return (
        <React.Fragment>
            <div style={{display: method == null ? 'block' : 'none'}}>
                <SearchField value={method} search={search} select={select}>
                    Search a method...
                </SearchField>
            </div>
            {method == null ? null : (
                <div className="mb-0 alert alert-info">
                    <strong>{method.psimi_id}</strong> - {method.name}
                    <button
                        type="button"
                        className="close"
                        onClick={unselect}
                    >
                        <span>&times;</span>
                    </button>
                </div>
            )}
        </React.Fragment>
    )
}

export default MethodField
