import React, { useState, useEffect } from 'react'

import { methods as api } from '../api'

import { Method } from '../types'

import { MethodAlert } from './MethodAlert'
import { SearchField } from './Shared/SearchField'

type Props = {
    psimi_id: string,
    query: string,
    update: (query: string) => void,
    select: (psimi_id: string) => void,
    unselect: () => void
}

export const MethodFieldset: React.FC<Props> = ({ psimi_id, ...props }) => {
    const [method, setMethod] = useState<Method>(null)

    useEffect(() => { api.select(psimi_id).then(m => setMethod(m)) }, [psimi_id])

    return (
        <fieldset>
            <legend>
                <span className="fas fa-circle small text-info"></span>&nbsp;Method
            </legend>
            <div className="row">
                <div className="col">
                    {method == null
                        ? <SearchField {...props} search={api.search} placeholder="Search a method..." />
                        : <MethodAlert {...props} method={method} />
                    }
                </div>
            </div>
        </fieldset>
    )
}
