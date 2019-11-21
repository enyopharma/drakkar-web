import React from 'react'

import { Method } from '../types'

type Props = {
    method: Method,
    unselect: () => void,
}

export const MethodAlert: React.FC<Props> = ({ method, unselect }) => {
    return (
        <div className="mb-0 alert alert-info">
            <strong>{method.psimi_id}</strong> - {method.name}
            <button type="button" className="close" onClick={e => unselect()}>
                <span>&times;</span>
            </button>
        </div>
    )
}
