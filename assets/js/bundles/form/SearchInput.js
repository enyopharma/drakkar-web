import React, { useState } from 'react'

const SearchInput = ({ search, select, format, children }) => {
    const [visible, setVisible] = useState(false);
    const [active, setActive] = useState(0);
    const [results, setResults] = useState([]);

    const handleChange = e => {
        setActive(0)
        setVisible(e.target.value.trim() != '')
        search(e.target.value.trim(), setResults)
    }

    const handleKeyDown = e => {
        if (e.keyCode == 13) {
            setVisible(false)
            select(results[active])
        }

        if (e.keyCode == 38 && visible) {
            setActive(active == 0 ? results.length : active - 1)
        }

        if (e.keyCode == 40 && visible) {
            setActive((active + 1) % results.length)
        }

        if (e.keyCode == 38 || e.keyCode == 40) {
            setVisible(true)
        }
    }

    const resultSelected = (index, result) => {
        setActive(index)
        setVisible(false)
        select(result)
    }

    return (
        <React.Fragment>
            <input
                type="text"
                placeholder={children}
                className="form-control"
                onFocus={e => setVisible(true)}
                onBlur={e => setVisible(false)}
                onChange={handleChange}
                onKeyDown={handleKeyDown}
            />
            {! visible ? null : (
            <div style={{position: 'relative'}}>
                <div style={{position: 'absolute', width: '100%', zIndex: 100}}>
                    <ul className="list-group">
                    {results.slice(0, 5).map((result, index) => (
                        <li
                            key={index}
                            className={'list-group-item' + (active == index ? ' active' : '')}
                            onMouseOver={e => setActive(index)}
                            onMouseDown={e => resultSelected(index, result)}
                        >
                            {format(result)}
                        </li>
                    ))}
                    </ul>
                </div>
            </div>
            )}
        </React.Fragment>
    )
}

export default SearchInput
