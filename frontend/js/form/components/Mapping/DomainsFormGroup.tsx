import React, { useState } from 'react'

import { Domain, ScaledDomain } from '../../src/types'

type Props = {
    domains: ScaledDomain[]
    enabled?: boolean,
    select: (domain: Domain) => void,
}

export const DomainsFormGroup: React.FC<Props> = ({ domains, enabled = true, select, children }) => {
    const [domain, setDomain] = useState<number | null>(null)

    const handleChange = (e: any) => {
        setDomain(e.target.value == '' ? null : parseInt(e.target.value))
    }

    const submit = () => {
        if (domain != null) select(domains[domain])
    }

    return (
        <div className="row">
            <div className="col">
                <select
                    value={domain == null ? '' : domain}
                    className="form-control"
                    onChange={e => handleChange(e)}
                    disabled={domains.length == 0}
                >
                    <option value="">Please select a domain</option>
                    {domains.map((domain, i) => (
                        <option key={i} value={i} disabled={!domain.valid}>
                            {domain.key} - {domain.description} [{domain.valid
                                ? [domain.start, domain.stop].join(', ')
                                : 'out of selected sequence'
                            }]
                        </option>
                    ))}
                </select>
            </div>
            <div className="col-3">
                <button
                    type="button"
                    className="btn btn-block btn-info"
                    onClick={e => submit()}
                    disabled={!enabled || domain == null}
                >
                    {children}
                </button>
            </div>
        </div>
    )
}
