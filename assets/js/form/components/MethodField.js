import React from 'react'

import api from '../api'
import SearchField from './SearchField'

const MethodField = ({ method, select, unselect }) => {
    const searchMethods = (q, handler) => {
        api.method.search(q, methods => {
            handler(methods.map(method => ({
                value: method,
                label: [method.psimi_id, method.name].join(' - '),
            })))
        })
    }

    const selectMethod = method => select(method)

    return (
        <React.Fragment>
            <div style={{display: method == null ? 'block' : 'none'}}>
                <SearchField value={method} search={searchMethods} select={selectMethod}>
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
