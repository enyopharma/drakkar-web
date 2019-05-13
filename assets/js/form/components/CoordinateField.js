import React from 'react'

const CoordinateField = ({ value, set, max, valid = true, children }) => {
    const handleChange = (e) => {
        if (e.target.value == '') { set(''); return }

        let value = parseInt(e.target.value)
        if (value < 1) value = 1
        if (value > max) value = max
        set(value)
    }

    return (
        <input
            type="number"
            min="1"
            max={max}
            className={'form-control' + (valid ? '' : ' is-invalid')}
            placeholder={children}
            value={value}
            onChange={handleChange}
        />
    )
}

export default CoordinateField
