import React from 'react'
import { FaCircle } from 'react-icons/fa'

import { MethodProps } from '../src/props'

import { MethodAlert } from './MethodAlert'
import { MethodSearchField } from './MethodSearchField'

export const MethodFieldset: React.FC<MethodProps> = ({ method, actions, ...props }) => {
    return (
        <fieldset>
            <legend>
                <span className="small text-info"><FaCircle /></span>&nbsp;Method
            </legend>
            <div className="row">
                <div className="col">
                    {method == null
                        ? <MethodSearchField {...props} {...actions} />
                        : <MethodAlert {...props} {...actions} method={method} />
                    }
                </div>
            </div>
        </fieldset>
    )
}
