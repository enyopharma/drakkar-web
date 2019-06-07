import React, { useState } from 'react'

const areContiguous = filtered => {
    let last = 0

    for (let i = 0; i < filtered.length; i++) {
        if (last != 0 && filtered[i].start != last + 1) {
            return false
        }
        last = filtered[i].stop
    }

    return true
}

const ChainsFormGroup = ({ chains, enabled = true, set, children }) => {
    const [selected, setSelected] = useState([])

    const filtered = selected.map(s => chains[s]).sort((a, b) => a.start - b.start)

    const isValid = areContiguous(filtered)

    const update = options => {
        setSelected([...options]
            .filter(o => o.selected)
            .map(o => parseInt(o.value))
        )
    }

    const handleClick = () => {
        set(filtered[0].start, filtered[filtered.length - 1].stop)
    }

    return (
        <div className="row">
            <div className="col">
                <select
                    value={selected}
                    className="form-control"
                    onChange={e => update(e.target.options)}
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
                    onClick={handleClick}
                    disabled={! enabled || ! isValid || filtered.length == 0}
                >
                    {children}
                </button>
            </div>
        </div>
    )
}

export default ChainsFormGroup
