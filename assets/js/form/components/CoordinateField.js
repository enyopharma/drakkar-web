import React from 'react'

const CoordinateField = ({ value, max, valid = true, set, children }) => {
    const setCoordinate = (value) => {
        if (value == '') { set(''); return }

        let v = parseInt(value)
        if (v < 1) v = 1
        if (v > max) v = max
        set(v)
    }

    return (
        <input
            type="number"
            min="1"
            max={max}
            className={'form-control' + (valid ? '' : ' is-invalid')}
            placeholder={children}
            value={value}
            onChange={e => setCoordinate(e.target.value)}
        />
    )
}

export default CoordinateField
