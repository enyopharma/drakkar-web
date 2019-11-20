import React, { useState, useEffect } from 'react'

import { Method } from '../types'

import { methods as api } from '../api'
import { MethodSection } from './MethodSection'

type Props = {
    psimi_id: string | null,
    query: string,
    update: (query: string) => void,
    select: (psimi_id: string) => void,
    unselect: () => void
}

export const MethodFieldset: React.FC<Props> = ({ psimi_id, ...props }) => {
    const [method, setMethod] = useState<Method>(null)

    useEffect(() => {
        psimi_id == null
            ? setMethod(null)
            : api.select(psimi_id).then(method => setMethod(method))
    }, [psimi_id])

    return (
        <fieldset>
            <legend>
                <span className="fas fa-circle small text-info"></span>
                &nbsp;
                Method
            </legend>
            <MethodSection {...props} method={method} />
        </fieldset>
    )
}
