import React from 'react'
import { useAction } from '../../src/hooks'

import { Method } from '../../src/types'
import { unselectMethod } from '../../src/reducer'

type Props = {
    method: Method,
}

export const MethodAlert: React.FC<Props> = ({ method }) => {
    const unselect = useAction(unselectMethod)

    return (
        <div className="alert alert-info">
            <strong>{method.psimi_id}</strong> - {method.name}
            <button type="button" className="close" onClick={e => unselect()}>
                <span>&times;</span>
            </button>
        </div>
    )
}
