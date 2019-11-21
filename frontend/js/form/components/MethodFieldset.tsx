import React, { useState, useEffect } from 'react'

import { methods as api } from '../src/api'

import { Method } from '../src/types'
import { MethodProps } from '../src/props'

import { MethodAlert } from './MethodAlert'
import { SearchField } from './Shared/SearchField'

export const MethodFieldset: React.FC<MethodProps> = ({ psimi_id, ...props }) => {
    const [method, setMethod] = useState<Method>(null)

    useEffect(() => { api.select(psimi_id).then(m => setMethod(m)) }, [psimi_id])

    const search = api.search
    const actions = props.actions

    return (
        <fieldset>
            <legend>
                <span className="fas fa-circle small text-info"></span>&nbsp;Method
            </legend>
            {method == null
                ? <SearchField {...props} {...actions} search={search} placeholder="Search a method..." />
                : <MethodAlert {...props} {...actions} method={method} />
            }
        </fieldset>
    )
}
