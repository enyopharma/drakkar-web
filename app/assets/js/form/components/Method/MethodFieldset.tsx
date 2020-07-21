import React from 'react'

import { MethodAlert } from './MethodAlert'
import { MethodSearchField } from './MethodSearchField'

import { methods as api } from '../../src/api'

type Props = {
    psimi_id: string | null,
    select: (psimi_id: string) => void,
    unselect: () => void,
}

export const MethodFieldset: React.FC<Props> = ({ psimi_id, select, unselect }) => (
    <React.Suspense fallback={null}>
        <fieldset>
            <legend>Method</legend>
            <div className="row">
                <div className="col">
                    {psimi_id == null
                        ? <MethodSearchField select={select} />
                        : <MethodAlertLoader psimi_id={psimi_id} unselect={unselect} />}
                </div>
            </div>
        </fieldset>
    </React.Suspense>
)

const MethodAlertLoader: React.FC<{ psimi_id: string, unselect: () => void }> = ({ psimi_id, ...props }) => {
    const method = api.select(psimi_id).read()

    return <MethodAlert method={method} {...props} />
}
