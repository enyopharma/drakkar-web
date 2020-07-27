import React, { useState } from 'react'
import { useAction } from '../../src/hooks'

import { Mature, InteractorI } from '../../src/types'
import { updateMature } from '../../src/reducer'

type Props = {
    i: InteractorI,
    matures: Mature[],
}

export const MatureProteinList: React.FC<Props> = ({ i, matures }) => {
    const select = useAction(updateMature)
    const [active, setActive] = useState<number | null>(null)

    return (
        <ul className="list-group">
            {matures.map((mature, index) => (
                <li
                    key={index}
                    className={'list-group-item' + (index == active ? ' active' : '')}
                    onClick={e => select({ i, mature: matures[index] })}
                    onMouseOut={e => setActive(null)}
                    onMouseOver={e => setActive(index)}
                >
                    <strong>{mature.name}</strong> - {mature.start} - {mature.stop}
                </li>
            ))}
        </ul>
    )
}
