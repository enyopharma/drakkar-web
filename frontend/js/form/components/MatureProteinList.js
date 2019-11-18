import React, { useState } from 'react'

const MatureProteinList = ({ matures, select }) => {
    const [active, setActive] = useState(null)

    return matures.length == 0 ? (
        <p>
            No mature protein.
        </p>
    ) : (
            <ul className="list-group">
                {matures.map((mature, index) => (
                    <li
                        key={index}
                        className={'list-group-item' + (index == active ? ' active' : '')}
                        onClick={e => select(matures[index])}
                        onMouseOut={e => setActive(null)}
                        onMouseOver={e => setActive(index)}
                    >
                        <strong>{mature.name}</strong> - {mature.start} - {mature.stop}
                    </li>
                ))}
            </ul>
        )
}

export default MatureProteinList
