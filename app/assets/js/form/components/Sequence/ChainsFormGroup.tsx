import React, { useState } from 'react'
import { Chain } from '../../src/types'

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

    const disabled = !enabled || !areContiguous(filtered) || filtered.length == 0

    const handleChange = (options: any) => {
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
                    onChange={e => handleChange(e.target.options)}
                    disabled={chains.length == 0}
                    multiple={true}
                >
                    {chains.map((chain, i) => <ChainOption key={i} i={i} chain={chain} />)}
                </select>
            </div>
            <div className="col-3">
                <button
                    type="button"
                    className="btn btn-block btn-info"
                    onClick={e => submit()}
                    disabled={disabled}
                >
                    {children}
                </button>
            </div>
        </div>
    )
}

const ChainOption: React.FC<{ i: number, chain: Chain }> = ({ i, chain }) => {
    const label = `${chain.description} [${chain.start}, ${chain.stop}]`

    return <option value={i}>{label}</option>
}
