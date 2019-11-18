import React from 'react'

import api from '../api'
import SearchField from './SearchField'

const MethodSection = ({ query, method, update, select, unselect }) => {
    const search = q => api.methods.search(q).then(methods => {
        return methods.map(method => ({
            value: method.psimi_id, label: [
                method.psimi_id,
                method.name,
            ].join(' - '),
        }))
    })

    return (
        <div className="row">
            <div className="col">
                <div style={{ display: method == null ? 'block' : 'none' }}>
                    <SearchField query={query} update={update} search={search} select={select}>
                        Search a method...
                    </SearchField>
                </div>
                {method == null ? null : (
                    <div className="mb-0 alert alert-info">
                        <strong>{method.psimi_id}</strong> - {method.name}
                        <button
                            type="button"
                            className="close"
                            onClick={e => unselect()}
                        >
                            <span>&times;</span>
                        </button>
                    </div>
                )}
            </div>
        </div>
    )
}

export default MethodSection
