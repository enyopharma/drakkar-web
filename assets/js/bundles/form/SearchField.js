import React, { useState } from 'react'

const SearchField = ({ value, results, search, select, format, children }) => {
    const max = 5;

    const [active, setActive] = useState(0)
    const [visible, setVisible] = useState(false)

    const selectResult = (result) => {
        search('')
        setVisible(false)
        setActive(0)
        select(result)
    }

    const handleChange = e => {
        setVisible(true)
        setActive(0)
        search(e.target.value.trim())
    }

    const handleKeyDown = e => {
        if (e.keyCode == 13) {
            setVisible(false)
            selectResult(results[active])
        }

        const length = results.slice(0, max).length

        if (e.keyCode == 27) {
            setVisible(false)
            setActive(0)
        }

        if (e.keyCode == 38 && visible) {
            setActive(active == 0 ? length - 1 : active - 1)
        }

        if (e.keyCode == 40 && visible) {
            setActive((active + 1) % length)
        }

        if (e.keyCode == 38 || e.keyCode == 40) {
            setVisible(true)
        }
    }

    return (
        <React.Fragment>
            <input
                type="text"
                placeholder={children}
                className="form-control"
                value={value}
                onFocus={e => setVisible(true)}
                onBlur={e => setVisible(false)}
                onChange={handleChange}
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
                            onMouseDown={e => selectResult(result)}
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

export default SearchField
