import React from 'react'

const MappingImg = ({ type, start, stop, width, active = true }) => {
    const startp = (start, length) => ((start - 1) * 100 / length) + '%'
    const stopp = (stop, length) => (stop * 100 / length) + '%'
    const widthp = (start, stop, length) => ((stop - start + 1) * 100 / length) + '%'

    return (
        <svg className="alignment" width="100%" height="30">
            <text x={startp(start, width)} y="10" fontSize="10">
                {start}
            </text>
            <text x={stopp(stop, width)} y="30" fontSize="10" textAnchor="end">
                {stop}
            </text>
            <rect x="0" y="16" width="100%" height="2"></rect>
            <rect
                className={active ? type : ''}
                x={startp(start, width)}
                y="14"
                width={widthp(start, stop, width)}
                height="6"
            ></rect>
        </svg>
    )
}

export default MappingImg
