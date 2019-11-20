import React, { useState, useRef, useEffect } from 'react'

import { SearchResult } from '../types'

type Props = {
    query: string,
    max?: number
    placeholder?: string,
    update: (query: string) => void,
    search: (query: string) => Promise<SearchResult[]>,
    select: (value: string) => void,
}

export const SearchField: React.FC<Props> = ({ query, update, search, select, max = 5, placeholder }) => {
    const timeout = useRef(null)
    const [results, setResults] = useState<SearchResult[]>([]);
    const [visible, setVisible] = useState<boolean>(false)
    const [active, setActive] = useState<number>(0)
    const [searching, setSearching] = useState<boolean>(false)

    useEffect(() => {
        if (timeout.current) clearTimeout(timeout.current)

        setResults([])
        setSearching(query.trim() != '')

        if (query.trim() != '') {
            timeout.current = setTimeout(() => {
                search(query)
                    .then(results => setResults(results))
                    .catch(error => console.log(error))
                    .finally(() => setSearching(false))
            }, 400)
        }
    }, [query])

    useEffect(() => setActive(0), [results])
    useEffect(() => setVisible(!searching), [searching])

    const regex = query.trim()
        .replace(/\s*\+$/, '')
        .replace(/\s*\+\s*/g, '|');

    const highlight = value => {
        return regex.length > 0
            ? value.replace(new RegExp('(' + regex + ')', 'gi'), '<strong>$1</strong>')
            : value
    }

    const selectValue = value => {
        setVisible(false)
        select(value)
    }

    const handleKeyDown = e => {
        const length = results.length > max
            ? results.slice(0, max).length
            : results.length

        if (!visible && (e.keyCode == 38 || e.keyCode == 40)) {
            setVisible(true)
        }

        if (visible && e.keyCode == 27) {
            setVisible(false)
        }

        if (visible && e.keyCode == 13 && results[active]) {
            selectValue(results[active].value)
        }

        if (visible && e.keyCode == 38) {
            setActive(active == 0 ? length - 1 : active - 1)
        }

        if (visible && e.keyCode == 40) {
            setActive((active + 1) % length)
        }
    }

    return (
        <React.Fragment>
            <div className="input-group">
                <div className="input-group-prepend">
                    <span className="input-group-text">
                        {searching
                            ? <span className="spinner-border spinner-border-sm"></span>
                            : <span className="fas fa-search"></span>
                        }
                    </span>
                </div>
                <input
                    type="text"
                    placeholder={placeholder}
                    className="form-control form-control-lg"
                    value={query}
                    onClick={e => setVisible(true)}
                    onFocus={e => setVisible(true)}
                    onBlur={e => setVisible(false)}
                    onChange={e => update(e.target.value)}
                    onKeyDown={handleKeyDown}
                />
            </div>
            <div style={{ position: 'relative', display: visible && results.length > 0 ? 'block' : 'none' }}>
                <div style={{ position: 'absolute', width: '100%', zIndex: 100 }}>
                    <ul className="list-group">
                        {results.slice(0, max).map((result, index) => (
                            <li
                                key={index}
                                className={'list-group-item' + (active == index ? ' active' : '')}
                                onMouseOver={e => setActive(index)}
                                onMouseDown={e => selectValue(result.value)}
                                dangerouslySetInnerHTML={{ __html: highlight(result.label) }}
                            >
                            </li>
                        ))}
                    </ul>
                </div>
            </div>
        </React.Fragment>
    )
}
