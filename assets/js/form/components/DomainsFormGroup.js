import React, { useState } from 'react'

const DomainsFormGroup = ({ domains, enabled = true, select, children }) => {
    const [domain, setDomain] = useState('')

    const handleClick = () => {
        select(domains[domain])
    }

    return (
        <div className="row">
            <div className="col">
                <select
                    value={domain}
                    className="form-control"
                    onChange={e => setDomain(e.target.value)}
                    disabled={domains.length == 0}
                >
                    <option value="">Please select a domain</option>
                    {domains.map((domain, i) => (
                        <option key={i} value={i} disabled={! domain.valid}>
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
                    onClick={handleClick}
                    disabled={! enabled || domain == ''}
                >
                    {children}
                </button>
            </div>
        </div>
    )
}

export default DomainsFormGroup
