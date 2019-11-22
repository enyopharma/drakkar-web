import React from 'react'

import { methods as api } from '../src/api'
import { MethodProps } from '../src/props'

import { MethodAlert } from './MethodAlert'
import { SearchField } from './Shared/SearchField'

export const MethodFieldset: React.FC<MethodProps> = ({ actions, ...props }) => {
    return (
        <fieldset>
            <legend>
                <span className="fas fa-circle small text-info"></span>&nbsp;Method
            </legend>
            {props.method == null
                ? <SearchField {...props} {...actions} search={api.search} placeholder="Search a method..." />
                : <MethodAlert {...props} {...actions} />
            }
        </fieldset>
    )
}
