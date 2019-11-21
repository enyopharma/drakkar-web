import React from 'react'

type Props = {
    value: number,
    max: number,
    valid?: boolean,
    placeholder?: string
    set: (value: number) => void,
}

export const CoordinateField: React.FC<Props> = ({ value, max, valid = true, placeholder = '', set }) => {
    const setCoordinate = (value: string): void => {
        if (value == '') { set(null); return }

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
            placeholder={placeholder}
            value={value == null ? '' : value}
            onChange={e => setCoordinate(e.target.value)}
        />
    )
}
