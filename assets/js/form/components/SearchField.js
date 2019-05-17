import React, { useState, useEffect } from 'react'

let timeout = null;

const SearchField = ({ search, select, display = true, max=5, children }) => {
    const [query, setQuery] = useState('');
    const [results, setResults] = useState([]);
    const [visible, setVisible] = useState(false)
    const [active, setActive] = useState(0)

    useEffect(() => {
        if (timeout) clearTimeout(timeout)

        if (query.trim() == '') return

        timeout = setTimeout(() => {
            search(query).then(results => setResults(results))
        }, 100)
    }, [query])

    useEffect(() => setActive(0), [visible])

    const selectValue = value => {
        setVisible(false)
        select(value)
    }

    const handleKeyDown = e => {
        const k = e.keyCode;

        if (k == 13 || k == 27 || k == 38 || k == 40) {
            const length = results.length > max
                ? results.slice(0, max).length
                : results.length

            if (k == 13) selectValue(results[active].value)
            if (k == 38) setActive(active == 0 ? length - 1 : active - 1)
            if (k == 40) setActive((active + 1) % length)
        }
    }

    return ! display ? null : (
        <React.Fragment>
            <input
                type="text"
                placeholder={children}
                className="form-control"
                value={query}
                onClick={e => setVisible(true)}
                onFocus={e => setVisible(true)}
                onBlur={e => setVisible(false)}
                onChange={e => setQuery(e.target.value)}
                onKeyDown={handleKeyDown}
            />
            {! visible ? null : (
            <div style={{position: 'relative'}}>
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
            )}
        </React.Fragment>
    )
}

export default SearchField
