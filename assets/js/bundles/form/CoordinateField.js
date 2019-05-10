import React from 'react'

const CoordinateField = ({ value, update, max, children }) => {
    const handleChange = (e) => {
        if (e.target.value == '') { update(''); return }

        let value = parseInt(e.target.value)
        if (value < 1) value = 1
        if (value > max) value = max
        update(value)
    }

    return (
        <input
            type="number"
            min="1"
            max={max}
            className="form-control"
            placeholder={children}
            value={value}
            onChange={handleChange}
        />
    )
}

export default CoordinateField
