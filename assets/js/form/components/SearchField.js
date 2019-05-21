import React, { useState, useRef, useEffect } from 'react'

const SearchField = ({ search, select, max=5, children }) => {
    const timeout = useRef(null)
    const [query, setQuery] = useState('');
    const [results, setResults] = useState([]);
    const [visible, setVisible] = useState(false)
    const [active, setActive] = useState(0)

    useEffect(() => {
        if (timeout.current) clearTimeout(timeout.current)

        timeout.current = setTimeout(() => {
            query.trim() == ''
                ? setResults([])
                : search(query).then(results => setResults(results))
        }, 100)
    }, [query])

    useEffect(() => setActive(0), [results])

    const selectValue = value => {
        setVisible(false)
        select(value)
    }

    const handleKeyDown = e => {
        if (e.keyCode == 27) {
            setVisible(false)
            return
        }

        if (! visible && (e.keyCode == 38 || e.keyCode == 40)) {
            setVisible(true)
            return
        }

        if (e.keyCode == 13 && results[active]) {
            selectValue(results[active].value)
            return
        }

        const length = results.length > max
            ? results.slice(0, max).length
            : results.length

        if (e.keyCode == 38) setActive(active == 0 ? length - 1 : active - 1)
        if (e.keyCode == 40) setActive((active + 1) % length)
    }

    return (
        <React.Fragment>
            <input
                type="text"
                placeholder={children}
                className="form-control"
                value={query}
                onFocus={e => setVisible(true)}
                onBlur={e => setVisible(false)}
                onChange={e => setQuery(e.target.value)}
                onKeyDown={handleKeyDown}
                style={{height: 'auto', padding: '0.75rem'}}
            />
            <div style={{position: 'relative', display: visible && results.length > 0 ? 'block' : 'none'}}>
                <div style={{position: 'absolute', width: '100%', zIndex: 100}}>
                    <ul className="list-group">
                    {results.slice(0, max).map((result, index) => (
                        <li
                            key={index}
                            className={'list-group-item' + (active == index ? ' active' : '')}
                            onMouseOver={e => setActive(index)}
                            onMouseDown={e => selectValue(result.value)}
                        >
                            {result.label}
                        </li>
                    ))}
                    </ul>
                </div>
            </div>
        </React.Fragment>
    )
}

export default SearchField
