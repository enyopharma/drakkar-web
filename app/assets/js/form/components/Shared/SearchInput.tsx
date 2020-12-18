import React, { useRef, useState, useEffect, ChangeEvent } from 'react'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faSearch } from '@fortawesome/free-solid-svg-icons/faSearch'

import { SearchType, Resource, SearchResult } from '../../src/types'

const classes: Record<SearchType, string> = {
    'method': 'progress-bar progress-bar-striped progress-bar-animated bg-info',
    'human': 'progress-bar progress-bar-striped progress-bar-animated bg-primary',
    'virus': 'progress-bar progress-bar-striped progress-bar-animated bg-danger',
}

const isQueryEmpty = (query: string | undefined) => query === undefined || query.trim().length === 0

type SearchInputProps = {
    type: SearchType
    query: string
    resource: Resource<SearchResult[]>
    update: (query: string) => void
    select: (id: number) => void
    placeholder?: string
    help?: string | null
}

export const SearchInput: React.FC<SearchInputProps> = ({ type, query, resource, update, select, placeholder = '', help = null }) => {
    const input = useRef<HTMLInputElement>(null)

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
                    onChange={e => update(e.target.value)}
                />
            </div>
            <SearchOverlay input={input}>
                <React.Suspense fallback={<ProgressBar type={type} />}>
                    <SearchResultList input={input} query={query} resource={resource} select={select} />
                </React.Suspense>
            </SearchOverlay>
        </div>
    )
}

type ProgressBarProps = {
    type: SearchType
}

const ProgressBar: React.FC<ProgressBarProps> = ({ type }) => (
    <ul className="list-group">
        <li className="list-group-item">
            <div className="progress">
                <div className={classes[type]} style={{ width: '100%' }}></div>
            </div>
        </li>
    </ul>
)

type SearchOverlayProps = {
    input: React.RefObject<HTMLInputElement>
}

const SearchOverlay: React.FC<SearchOverlayProps> = ({ input, children }) => {
    const [display, setDisplay] = useState<boolean>(false)

    useEffect(() => {
        const elem = input.current

        const focus = () => setDisplay(!isQueryEmpty(elem?.value))
        const blur = () => setDisplay(false)

        const keyup = (e: KeyboardEvent) => {
            switch (e.key) {
                case 'ArrowUp':
                    setDisplay(!isQueryEmpty(elem?.value))
                    break
                case 'ArrowDown':
                    setDisplay(!isQueryEmpty(elem?.value))
                    break
                case 'Escape':
                    setDisplay(false)
                    break
                default:
                    setDisplay(!isQueryEmpty(elem?.value))
            }
        }

        elem?.addEventListener('focus', focus)
        elem?.addEventListener('blur', blur)
        elem?.addEventListener('keyup', keyup)

        return () => {
            elem?.removeEventListener('focus', focus)
            elem?.removeEventListener('blur', blur)
            elem?.removeEventListener('keyup', keyup)
        }
    })

    return display === false ? null : (
        <div style={{ position: 'relative' }}>
            <div style={{ position: 'absolute', width: '100%', zIndex: 100 }}>
                {children}
            </div>
        </div>
    )
}

type SearchResultListProps = {
    input: React.RefObject<HTMLInputElement>
    query: string
    resource: Resource<SearchResult[]>
    select: (id: number) => void
}

const SearchResultList: React.FC<SearchResultListProps> = ({ input, query, resource, select }) => {
    const results = resource.read()

    const [active, setActive] = useState<number>(0)

    const active1 = results.length == 0 ? 0 : active % results.length
    const active2 = active1 >= 0 ? active1 : active1 + results.length

    const regex = query.trim()
        .replace(/\s*\+$/, '')
        .replace(/\s*\+\s*/g, '|');

    const highlighted = results.map(result => regex.length > 0
        ? { id: result.id, label: result.label.replace(new RegExp('(' + regex + ')', 'gi'), '<strong>$1</strong>') }
        : result
    )

    const keydown = (e: KeyboardEvent) => {
        if (e.key === 'ArrowUp') setActive(active - 1)
        if (e.key === 'ArrowDown') setActive(active + 1)
        if (e.key === 'Enter' && results[active2]) {
            select(results[active2].id)
        }
    }

    useEffect(() => {
        const elem = input.current

        elem?.addEventListener('keydown', keydown)

        return () => {
            elem?.removeEventListener('keydown', keydown)
        }
    })

    return (
        <ul className="list-group">
            {results.length == 0
                ? <li className="list-group-item">No entry found</li>
                : highlighted.map((result, index) => (
                    <li
                        key={index}
                        data-type="result"
                        data-index={index}
                        data-value={result.id}
                        className={'list-group-item' + (index == active2 ? ' active' : '')}
                        dangerouslySetInnerHTML={{ __html: result.label }}
                        onMouseDown={() => select(result.id)}
                        onMouseOver={() => setActive(index)}
                    >
                    </li>
                ))
            }
        </ul>
    )
}
