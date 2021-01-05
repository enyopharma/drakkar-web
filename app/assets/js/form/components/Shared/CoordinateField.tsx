import React, { useCallback } from 'react'

type Props = {
    value: number | null
    max: number
    valid?: boolean
    placeholder?: string
    update: (value: number | null) => void
}

export const CoordinateField: React.FC<Props> = ({ value, max, valid = true, placeholder = '', update }) => {
    const supdate = useCallback((value: string): void => {
        if (value === '') { update(null); return }

        let v = parseInt(value)
        if (v < 1) v = 1
        if (v > max) v = max
        update(v)
    }, [max, update])

    return (
        <input
            type="number"
            min="1"
            max={max}
            className={valid ? 'form-control' : 'form-control is-invalid'}
            placeholder={placeholder}
            value={value ?? ''}
            onChange={e => supdate(e.target.value)}
        />
    )
}
