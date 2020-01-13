import React from 'react'

import { ProteinType } from '../types'

type Props = {
    type: ProteinType
    start: number
    stop: number
    length: number
    active?: boolean
}

export const SequenceImg: React.FC<Props> = ({ type, start, stop, length, active = true }) => {
    const startp = (start: number, length: number): string => {
        return ((start - 1) * 100 / length) + '%'
    }

    const stopp = (stop: number, length: number): string => {
        return (stop * 100 / length) + '%'
    }

    const widthp = (start: number, stop: number, length: number): string => {
        return ((stop - start + 1) * 100 / length) + '%'
    }

    return (
        <svg className="alignment" width="100%" height="30">
            <text x={startp(start, length)} y="10" fontSize="10">
                {start}
            </text>
            <text x={stopp(stop, length)} y="30" fontSize="10" textAnchor="end">
                {stop}
            </text>
            <rect x="0" y="16" width="100%" height="2"></rect>
            <rect
                className={active ? type : ''}
                x={startp(start, length)}
                y="14"
                width={widthp(start, stop, length)}
                height="6"
            ></rect>
        </svg>
    )
}
