import React, { useState, useEffect, RefObject } from 'react'
import { SearchResult } from '../../src/types'

type Props = {
    input: RefObject<HTMLInputElement>
    query: string
    search: (query: string) => SearchResult[]
    select: (id: number) => void
}

export const SearchResultList: React.FC<Props> = ({ query, input, search, select }) => {
    const results = search(query)

    const [active, setActive] = useState<number>(0)

    useEffect(() => setActive(0), [query])

    const onKeyDown = (e: KeyboardEvent) => {
        if (e.keyCode == 38) {
            e.preventDefault()
            setActive(active - 1)
        }

        if (e.keyCode == 40) {
            e.preventDefault()
            setActive(active + 1)
        }

        if (e.keyCode == 13 && results[active]) {
            e.preventDefault()
            select(results[active].id)
        }
    }

    useEffect(() => {
        const elem = input.current

        elem?.addEventListener('keydown', onKeyDown)

        return () => {
            elem?.removeEventListener('keydown', onKeyDown)
        }
    })

    const active1 = results.length == 0 ? 0 : active % results.length
    const active2 = active1 >= 0 ? active1 : active1 + results.length

    const regex = query.trim()
        .replace(/\s*\+$/, '')
        .replace(/\s*\+\s*/g, '|');

    const highlight = (label: string) => {
        return regex.length > 0
            ? label.replace(new RegExp('(' + regex + ')', 'gi'), '<strong>$1</strong>')
            : label
    }

    return (
        <ul className="list-group">
            {results.length == 0
                ? <li className="list-group-item">No entry found</li>
                : results.map((result, index) => (
                    <li
                        key={index}
                        data-type="result"
                        data-index={index}
                        data-value={result.id}
                        className={'list-group-item' + (index == active2 ? ' active' : '')}
                        dangerouslySetInnerHTML={{ __html: highlight(result.label) }}
                        onMouseDown={e => select(result.id)}
                        onMouseOver={e => setActive(index)}
                    >
                    </li>
                ))
            }
        </ul>
    )
}
