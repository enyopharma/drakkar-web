import React from 'react'

import { MethodProps } from '../src/props'

import { MethodAlert } from './MethodAlert'
import { MethodSearchField } from './MethodSearchField'

export const MethodFieldset: React.FC<MethodProps> = ({ method, actions, ...props }) => {
    return (
        <fieldset>
            <legend>
                <span className="fas fa-circle small text-info"></span>&nbsp;Method
            </legend>
            {method == null
                ? <MethodSearchField {...props} {...actions} />
                : <MethodAlert {...props} {...actions} method={method} />
            }
        </fieldset>
    )
}
