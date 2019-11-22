import React, { useState, useRef } from 'react'

import { SearchType, SearchResult } from '../../src/types'

import { SearchLoader } from './SearchLoader'
import { SearchResultList } from './SearchResultList'

type Props = {
    type: SearchType,
    query: string,
    search: (query: string) => SearchResult[],
    update: (query: string) => void,
    select: (value: string) => void,
    placeholder?: string,
    max?: number
}

export const SearchField: React.FC<Props> = ({ type, query, update, search, select, placeholder = '' }) => {
    const input = useRef<HTMLInputElement>(null)
    const [enabled, setEnabled] = useState<boolean>(false)

    const handleKeyDown = (e: any) => {
        if (e.keyCode == 27) {
            setEnabled(!enabled)
        }

        if (!enabled && (e.keyCode == 38 || e.keyCode == 40)) {
            setEnabled(true)
        }
    }

    return (
        <div>
            <div className="input-group">
                <div className="input-group-prepend">
                    <span className="input-group-text">
                        <span className="fas fa-search"></span>
                    </span>
                </div>
                <input
                    ref={input}
                    type="text"
                    placeholder={placeholder}
                    className="form-control form-control-lg"
                    value={query}
                    onFocus={e => setEnabled(true)}
                    onBlur={e => setEnabled(false)}
                    onChange={e => update(e.target.value)}
                    onKeyDown={e => handleKeyDown(e)}
                />
            </div>
            <React.Suspense fallback={<SearchLoader type={type} enabled={enabled} />}>
                <SearchResultList input={input} query={query} enabled={enabled} search={search} select={select} />
            </React.Suspense>
        </div>
    )
}
