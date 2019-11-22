import React, { useState, useEffect, MutableRefObject } from 'react'

import { SearchResult } from '../../src/types'

import { SearchOverlay } from './SearchOverlay'

type Props = {
    input: MutableRefObject<HTMLInputElement>
    query: string,
    enabled: boolean,
    search: (query: string) => SearchResult[],
    select: (value: string) => void,
}

export const SearchResultList: React.FC<Props> = ({ query, input, enabled, search, select }) => {
    const results = search(query)

    const [active, setActive] = useState<number>(0)

    useEffect(() => setActive(0), [query])

    if (input.current) {
        input.current.onkeydown = e => {
            if (enabled && e.keyCode == 38) {
                setActive(active - 1)
            }

            if (enabled && e.keyCode == 40) {
                setActive(active + 1)
            }

            if (enabled && e.keyCode == 13 && results[active]) {
                select(results[active].value)
            }
        }
    }

    const active1 = results.length == 0 ? 0 : active % results.length
    const active2 = active1 >= 0 ? active1 : active1 + results.length

    const regex = query.trim()
        .replace(/\s*\+$/, '')
        .replace(/\s*\+\s*/g, '|');

    const highlight = value => {
        return regex.length > 0
            ? value.replace(new RegExp('(' + regex + ')', 'gi'), '<strong>$1</strong>')
            : value
    }

    return !enabled || query == '' ? null : (
        <SearchOverlay>
            <ul className="list-group">
                {results.length == 0
                    ? (
                        <li className="list-group-item">
                            No entry found
                        </li>
                    )
                    : results.map((result, index) => (
                        <li
                            key={index}
                            data-type="result"
                            data-index={index}
                            data-value={result.value}
                            className={'list-group-item' + (index == active2 ? ' active' : '')}
                            dangerouslySetInnerHTML={{ __html: highlight(result.label) }}
                            onMouseDown={e => select(result.value)}
                            onMouseOver={e => setActive(index)}
                        >
                        </li>
                    ))
                }
            </ul>
        </SearchOverlay>
    )
}
