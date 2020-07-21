import React, { useState, useRef } from 'react'
import { FaSearch } from 'react-icons/fa'

import { SearchType, SearchResult } from '../../src/types'

import { SearchLoader } from './SearchLoader'
import { SearchResultList } from './SearchResultList'

type Props = {
    type: SearchType,
    search: (query: string) => SearchResult[],
    select: (value: string) => void,
    placeholder?: string,
    help?: string | null
}

export const SearchField: React.FC<Props> = ({ type, search, select, placeholder = '', help = null }) => {
    const input = useRef<HTMLInputElement>(null)
    const [query, setQuery] = useState<string>('')
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
                        <FaSearch />
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
                    onChange={e => setQuery(e.target.value)}
                    onKeyDown={e => handleKeyDown(e)}
                />
            </div>
            <React.Suspense fallback={<SearchLoader type={type} enabled={enabled} />}>
                <SearchResultList input={input} query={query} enabled={enabled} search={search} select={select} />
            </React.Suspense>
            {help == null ? null : (
                <small className="form-text text-muted">{help}</small>
            )}
        </div>
    )
}
