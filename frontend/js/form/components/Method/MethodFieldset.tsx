import React from 'react'

import { Method } from '../../src/types'

import { MethodAlert } from './MethodAlert'
import { MethodSearchField } from './MethodSearchField'

type Props = {
    method: Method | null,
    query: string,
    update: (query: string) => void,
    select: (psimi_id: string) => void,
    unselect: () => void,
}

export const MethodFieldset: React.FC<Props> = ({ method, ...props }) => {
    return (
        <fieldset>
            <legend>
                Method
            </legend>
            <div className="row">
                <div className="col">
                    {method == null
                        ? <MethodSearchField {...props} />
                        : <MethodAlert {...props} method={method} />
                    }
                </div>
            </div>
        </fieldset>
    )
}
