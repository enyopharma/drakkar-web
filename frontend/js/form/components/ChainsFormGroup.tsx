import React, { useState } from 'react'

import { Chain } from '../types'

type Props = {
    chains: Chain[],
    enabled?: boolean,
    set: (start: number, stop: number) => void,
}

const areContiguous = (chains: Chain[]): boolean => {
    let last = 0

    for (let i = 0; i < chains.length; i++) {
        if (last != 0 && chains[i].start != last + 1) {
            return false
        }
        last = chains[i].stop
    }

    return true
}

export const ChainsFormGroup: React.FC<Props> = ({ chains, enabled = true, set, children }) => {
    const [selected, setSelected] = useState<string[]>([])

    const filtered = selected.map(s => chains[parseInt(s)]).sort((a, b) => a.start - b.start)

    const isValid = areContiguous(filtered)

    const select = options => {
        setSelected([...options]
            .filter(o => o.selected)
            .map(o => o.value)
        )
    }

    const submit = () => {
        set(filtered[0].start, filtered[filtered.length - 1].stop)
    }

    return (
        <div className="row">
            <div className="col">
                <select
                    value={selected}
                    className="form-control"
                    onChange={e => select(e.target.options)}
                    disabled={chains.length == 0}
                    multiple={true}
                >
                    {chains.map((chain, i) => (
                        <option key={i} value={i}>
                            {chain.description} [{chain.start}, {chain.stop}]
                        </option>
                    ))}
                </select>
            </div>
            <div className="col-3">
                <button
                    type="button"
                    className="btn btn-block btn-info"
                    onClick={e => submit()}
                    disabled={!enabled || !isValid || filtered.length == 0}
                >
                    {children}
                </button>
            </div>
        </div>
    )
}
