import React from 'react'

const startp = (start, length) => ((start - 1) * 100/length) + '%'
const stopp = (stop, length) => (stop * 100/length) + '%'
const widthp = (start, stop, length) => ((stop - start + 1) * 100/length) + '%'

const MappingImg = ({ type, start, stop, length }) => (
    <svg className="mapping" width="100%" height="30">
        <text x="0" y="30" fontSize="10">
            1
        </text>
        <text x="100%" y="10" fontSize="10" textAnchor="end">
            {length}
        </text>
        <text x={startp(start, length)} y="10" fontSize="10">
            {start}
        </text>
        <text x={stopp(stop, length)} y="30" fontSize="10" textAnchor="end">
            {stop}
        </text>
        <rect x="0" y="16" width="100%" height="2"></rect>
        <rect
            className={type}
            x={startp(start, length)}
            y="14"
            width={widthp(start, stop, length)}
            height="6"
        ></rect>
    </svg>
)

export default MappingImg
