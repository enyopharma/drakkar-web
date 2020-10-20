import React, { useRef, useState, useEffect } from 'react'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faSearch } from '@fortawesome/free-solid-svg-icons/faSearch'

import { SearchType, SearchResult } from '../../src/types'

import { SearchOverlay } from './SearchOverlay'
import { SearchResultList } from './SearchResultList'

type Props = {
    type: SearchType
    search: (query: string) => SearchResult[]
    select: (id: number) => void
    placeholder?: string
    help?: string | null
}

const classes: Record<SearchType, string> = {
    'method': 'progress-bar progress-bar-striped progress-bar-animated bg-info',
    'human': 'progress-bar progress-bar-striped progress-bar-animated bg-primary',
    'virus': 'progress-bar progress-bar-striped progress-bar-animated bg-danger',
}

export const SearchField: React.FC<Props> = ({ type, search, select, placeholder = '', help = null }) => {
    const input = useRef<HTMLInputElement>(null)
    const [query, setQuery] = useState<string>('')
    const [enabled, setEnabled] = useState<boolean>(false)

    const handleKeyDown = (e: React.KeyboardEvent) => {
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
                        <FontAwesomeIcon icon={faSearch} />
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
            {enabled && query.trim().length > 0 && (
                <React.Suspense fallback={<ProgressBar type={type} />}>
                    <SearchOverlay>
                        <SearchResultList input={input} query={query} search={search} select={select} />
                    </SearchOverlay>
                </React.Suspense>
            )}
            {help && <small className="form-text text-muted">{help}</small>}
        </div>
    )
}

const ProgressBar: React.FC<{ type: SearchType }> = ({ type }) => (
    <ul className="list-group">
        <li className="list-group-item">
            <div className="progress">
                <div className={classes[type]} style={{ width: '100%' }}></div>
            </div>
        </li>
    </ul>
)
