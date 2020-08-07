import React from 'react'
import { methods as api } from '../../src/api'
import { useAppSelector } from '../../src/hooks'

import { MethodAlert } from './MethodAlert'
import { MethodSearchField } from './MethodSearchField'

type Props = {
    method_id: number
}

export const MethodFieldset: React.FC = () => {
    const { method_id } = useAppSelector(state => state)

    return (
        <React.Suspense fallback={null}>
            <fieldset>
                <legend>Method</legend>
                <div className="row">
                    <div className="col">
                        {method_id == null
                            ? <MethodSearchField />
                            : <MethodAlertLoader method_id={method_id} />}
                    </div>
                </div>
            </fieldset>
        </React.Suspense>
    )
}

const MethodAlertLoader: React.FC<Props> = ({ method_id }) => {
    const method = api.select(method_id).read()

    return <MethodAlert method={method} />
}
