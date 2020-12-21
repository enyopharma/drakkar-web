import React, { useState, useCallback } from 'react'

import { Domain, ScaledDomain } from '../../src/types'

type Props = {
    domains: ScaledDomain[]
    select: (domain: Domain) => void
    enabled?: boolean
}

export const DomainsFormGroup: React.FC<Props> = ({ domains, enabled = true, select, children }) => {
    const [selected, setSelected] = useState<number | null>(null)

    return (
        <div className="row">
            <div className="col">
                <DomainInput selected={selected} domains={domains} update={setSelected} />
            </div>
            <div className="col-3">
                <SubmitButton selected={selected} domains={domains} select={select} enabled={enabled}>
                    {children}
                </SubmitButton>
            </div>
        </div>
    )
}

type DomainInputProps = {
    selected: number | null
    domains: ScaledDomain[]
    update: (selected: number | null) => void
}

const DomainInput: React.FC<DomainInputProps> = ({ selected, domains, update }) => {
    const supdate = useCallback((value: string) => {
        update(value === '' ? null : parseInt(value))
    }, [update])

    return (
        <select
            value={selected ?? ''}
            className="form-control"
            onChange={e => supdate(e.target.value)}
            disabled={domains.length == 0}
        >
            <option value="">Please select a domain</option>
            {domains.map((domain, i) => <DomainOption key={i} i={i} domain={domain} />)}
        </select>
    )
}

type DomainOptionProps = {
    i: number
    domain: ScaledDomain
}

const DomainOption: React.FC<DomainOptionProps> = ({ i, domain }) => {
    const cdx = domain.valid
        ? `${domain.start}, ${domain.stop}`
        : 'out of selected sequence'

    const label = `${domain.type} - ${domain.description} [${cdx}]`

    return (
        <option value={i} disabled={!domain.valid}>
            {label}
        </option>
    )
}

type SubmitButtonProps = {
    selected: number | null
    domains: Domain[]
    select: (domain: Domain) => void
    enabled: boolean
}

const SubmitButton: React.FC<SubmitButtonProps> = ({ selected, domains, select, enabled, children }) => {
    const submit = useCallback(() => {
        if (selected != null) select(domains[selected])
    }, [selected, domains, select])

    return (
        <button
            type="button"
            className="btn btn-block btn-info"
            onClick={() => submit()}
            disabled={!enabled || selected === null}
        >
            {children}
        </button>
    )
}
