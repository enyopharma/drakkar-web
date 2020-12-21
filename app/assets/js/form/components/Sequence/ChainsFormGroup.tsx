import React, { useState, useCallback } from 'react'

import { Chain } from '../../src/types'

type ChainsFormGroupProps = {
    chains: Chain[]
    enabled?: boolean
    update: (start: number, stop: number) => void
}

export const ChainsFormGroup: React.FC<ChainsFormGroupProps> = ({ chains, enabled = true, update, children }) => {
    const [selected, setSelected] = useState<string[]>([])

    const filtered = selected.map(s => chains[parseInt(s)])

    return (
        <div className="row">
            <div className="col">
                <ChainSelectInput selected={selected} update={setSelected} chains={chains} />
            </div>
            <div className="col-3">
                <SubmitButton chains={filtered} enabled={enabled} update={update}>
                    {children}
                </SubmitButton>
            </div>
        </div>
    )
}

type ChainSelectInputProps = {
    selected: string[]
    update: (selected: string[]) => void
    chains: Chain[]
}

const ChainSelectInput: React.FC<ChainSelectInputProps> = ({ selected, update, chains }) => {
    const onChange = useCallback((options: HTMLOptionsCollection) => {
        const selected = []

        for (let i = 0; i < options.length; i++) {
            if (options[i].selected) {
                selected.push(options[i].value)
            }
        }

        update(selected)
    }, [update])

    return (
        <select
            value={selected}
            className="form-control"
            onChange={e => onChange(e.target.options)}
            disabled={chains.length == 0}
            multiple={true}
        >
            {chains.map((chain, i) => <ChainOption key={i} i={i} chain={chain} />)}
        </select>
    )
}

type ChainOptionProps = {
    i: number
    chain: Chain
}

const ChainOption: React.FC<ChainOptionProps> = ({ i, chain }) => {
    const label = `${chain.description} [${chain.start}, ${chain.stop}]`

    return <option value={i}>{label}</option>
}

type SubmitButtonProps = {
    chains: Chain[]
    enabled: boolean
    update: (start: number, stop: number) => void
}

const SubmitButton: React.FC<SubmitButtonProps> = ({ chains, enabled, update, children }) => {
    const disabled = !enabled || !areContiguous(chains) || chains.length == 0

    const sorted = chains.sort((a, b) => a.start - b.start)

    const submit = () => update(sorted[0].start, sorted[sorted.length - 1].stop)

    return (
        <button type="button" className="btn btn-block btn-info" onClick={() => submit()} disabled={disabled}>
            {children}
        </button>
    )
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
