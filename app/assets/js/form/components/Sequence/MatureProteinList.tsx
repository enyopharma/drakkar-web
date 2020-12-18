import React, { useState } from 'react'
import { useAction } from '../../src/hooks'
import { updateMature } from '../../src/reducer'
import { Mature, InteractorI } from '../../src/types'

type MatureProteinListProps = {
    i: InteractorI
    matures: Mature[]
}

export const MatureProteinList: React.FC<MatureProteinListProps> = ({ i, matures }) => {
    const select = useAction(updateMature)
    const [active, setActive] = useState<number | null>(null)

    return (
        <ul className="list-group" onMouseOut={() => setActive(null)}>
            {matures.map((mature, key) => (
                <li
                    key={key}
                    className={'list-group-item' + (active === key ? ' active' : '')}
                    onClick={() => select({ i, mature })}
                    onMouseOver={() => setActive(key)}
                >
                    <strong>{mature.name}</strong> - {mature.start} - {mature.stop}
                </li>
            ))}
        </ul>
    )
}
