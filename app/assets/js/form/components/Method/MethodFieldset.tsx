import React from 'react'
import { methods as api } from '../../src/api'
import { useMethodSelector } from '../../src/hooks'

import { MethodAlert } from './MethodAlert'
import { MethodSearchField } from './MethodSearchField'

type Props = {
    psimi_id: string,
}

export const MethodFieldset: React.FC = () => {
    const { psimi_id } = useMethodSelector(state => state)

    return (
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
}

const MethodAlertLoader: React.FC<Props> = ({ psimi_id }) => {
    const method = api.select(psimi_id).read()

    return <MethodAlert method={method} />
}
