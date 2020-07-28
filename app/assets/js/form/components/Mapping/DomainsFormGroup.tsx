import React, { useState } from 'react'
import { Domain, ScaledDomain } from '../../src/types'

type Props = {
    domains: ScaledDomain[]
    enabled?: boolean,
    select: (domain: Domain) => void,
}

export const DomainsFormGroup: React.FC<Props> = ({ domains, enabled = true, select, children }) => {
    const [domain, setDomain] = useState<number | null>(null)

    const disabled = !enabled || domain == null

    const handleChange = (value: string) => {
        setDomain(value == '' ? null : parseInt(value))
    }

    const submit = () => {
        if (domain != null) select(domains[domain])
    }

    return (
        <div className="row">
            <div className="col">
                <select
                    value={domain ?? ''}
                    className="form-control"
                    onChange={e => handleChange(e.target.value)}
                    disabled={domains.length == 0}
                >
                    <option value="">Please select a domain</option>
                    {domains.map((domain, i) => <DomainOption key={i} i={i} domain={domain} />)}
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

const DomainOption: React.FC<{ i: number, domain: ScaledDomain }> = ({ i, domain }) => {
    const cdx = domain.valid
        ? `${domain.start}, ${domain.stop}`
        : 'out of selected sequence'

    const label = `${domain.key} - ${domain.description} [${cdx}]`

    return (
        <option value={i} disabled={!domain.valid}>
            {label}
        </option>
    )
}
