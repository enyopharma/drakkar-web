import qs from 'query-string'
import fetch from 'cross-fetch'
import React, { useState, useEffect } from 'react'

import SearchField from './SearchField'

const MethodFieldset = ({ method, actions }) => {
    const search = q => fetch('/methods?' + qs.stringify({q: q}))
        .then(response => response.json(), error => console.log(error))
        .then(json => json.data.methods.map(method => ({
            value: method,
            label: [method.psimi_id, method.name].join(' - '),
        })))

    return (
        <fieldset>
            <legend>
                <i className="fas fa-circle small text-info"></i>
                &nbsp;
                Method
            </legend>
            <div className="form-group row">
                <div className="col">
                    <SearchField
                        display={method == null}
                        search={search}
                        select={actions.selectMethod}
                    >
                        Search a method...
                    </SearchField>
                    {method == null ? null : (
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
