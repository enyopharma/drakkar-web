import React from 'react'

import api from '../api'
import SearchField from './SearchField'

const MethodSection = ({ selected, select, unselect }) => {
    const search = q => api.method.search(q).then(methods => {
        return methods.map(method => ({
            value: method,
            label: [method.psimi_id, method.name].join(' - '),
        }))
    })

    return (
        <div className="row">
            <div className="col">
                <div style={{display: selected == null ? 'block' : 'none'}}>
                    <SearchField search={search} select={select}>
                        Search a method...
                    </SearchField>
                </div>
                {selected == null ? null : (
                    <div className="mb-0 alert alert-info">
                        <strong>{selected.psimi_id}</strong> - {selected.name}
                        <button
                            type="button"
                            className="close"
                            onClick={unselect}
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
