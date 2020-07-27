import React, { useState } from 'react'
import { useAction } from '../../src/hooks'
import { updateMature } from '../../src/reducer'
import { Mature, InteractorI } from '../../src/types'

type Props = {
    i: InteractorI,
    matures: Mature[],
}

const classes = (active: boolean) => 'list-group-item' + (active ? ' active' : '')

export const MatureProteinList: React.FC<Props> = ({ i, matures }) => {
    const select = useAction(updateMature)
    const [active, setActive] = useState<number | null>(null)

    return (
        <ul className="list-group">
            {matures.map((mature, index) => (
                <li
                    key={index}
                    className={classes(index == active)}
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
