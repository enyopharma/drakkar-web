import React from 'react'

import { SearchResult, Method } from '../types'

import { methods as api } from '../api'
import { SearchField } from './SearchField'

type Props = {
    query: string,
    method: Method,
    update: (query: string) => void,
    select: (method: string) => void,
    unselect: () => void
}

export const MethodSection: React.FC<Props> = ({ query, method, update, select, unselect }) => {
    const search = (q: string): Promise<SearchResult[]> => api.search(q).then(methods => {
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
                    <SearchField
                        query={query}
                        update={update}
                        search={search}
                        select={select}
                        placeholder="Search a method..."
                    />
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
