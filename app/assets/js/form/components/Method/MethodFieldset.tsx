import React from 'react'

import { MethodAlert } from './MethodAlert'
import { MethodSearchField } from './MethodSearchField'

import { methods as api } from '../../src/api'

type Props = {
    psimi_id: string | null,
}

export const MethodFieldset: React.FC<Props> = ({ psimi_id }) => (
    <React.Suspense fallback={null}>
        <fieldset>
            <legend>Method</legend>
            <div className="row">
                <div className="col">
                    {psimi_id == null
                        ? <MethodSearchField />
                        : <MethodAlertLoader psimi_id={psimi_id} />}
                </div>
            </div>
        </fieldset>
    </React.Suspense>
)

const MethodAlertLoader: React.FC<Props & { psimi_id: string }> = ({ psimi_id }) => {
    const method = api.select(psimi_id).read()

    return <MethodAlert method={method} />
}
