import React, { useState, useRef } from 'react'

import { SearchTypes, SearchResult } from '../../src/types'

import { SearchLoader } from './SearchLoader'
import { SearchResultList } from './SearchResultList'

type Props = {
    type: SearchTypes,
    query: string,
    search: (query: string) => SearchResult[],
    update: (query: string) => void,
    select: (value: string) => void,
    placeholder?: string,
    max?: number
}

export const SearchField: React.FC<Props> = ({ type, query, update, search, select, placeholder = '' }) => {
    const input = useRef(null)
    const [enabled, setEnabled] = useState<boolean>(false)

    const handleKeyDown = e => {
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

//const handleKeyDown = e => {
//    const length = results.length > max
//        ? results.slice(0, max).length
//        : results.length
//
//    if (!visible && (e.keyCode == 38 || e.keyCode == 40)) {
//        setVisible(true)
//    }
//
//    if (visible && e.keyCode == 27) {
//        setVisible(false)
//    }
//
//    if (visible && e.keyCode == 13 && results[active]) {
//        selectValue(results[active].value)
//    }
//
//    if (visible && e.keyCode == 38) {
//        setActive(active == 0 ? length - 1 : active - 1)
//    }
//
//    if (visible && e.keyCode == 40) {
//        setActive((active + 1) % length)
//    }
//}
