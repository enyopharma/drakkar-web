import React from 'react'
import { ProteinType } from '../../src/types'

type Props = {
    type: ProteinType
    start: number | null
    stop: number | null
    length: number
    active?: boolean
}

export const SequenceImg: React.FC<Props> = ({ type, start, stop, length, active = true }) => {
    if (start === null || stop === null) {
        return (
            <svg className="alignment" width="100%" height="30">
                <rect x="0" y="16" width="100%" height="2"></rect>
            </svg>
        )
    }

    const startp = `${(start - 1) * 100 / length}%`
    const stopp = `${stop * 100 / length}%`
    const widthp = `${(stop - start + 1) * 100 / length}%`

    return (
        <svg className="alignment" width="100%" height="30">
            <text x={startp} y="10" fontSize="10">
                {start}
            </text>
            <text x={stopp} y="30" fontSize="10" textAnchor="end">
                {stop}
            </text>
            <rect x="0" y="16" width="100%" height="2"></rect>
            <rect className={active ? type : ''} x={startp} y="14" width={widthp} height="6"></rect>
        </svg>
    )
}
