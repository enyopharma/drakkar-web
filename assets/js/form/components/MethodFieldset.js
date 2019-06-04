import React from 'react'

import MethodSection from './MethodSection'

const MethodSectionset = ({ method, actions }) => (
    <fieldset>
        <legend>
            <i className="fas fa-circle small text-info" />
            &nbsp;
            Method
        </legend>
        <MethodSection
            method={method}
            select={actions.selectMethod}
            unselect={actions.unselectMethod}
        />
    </fieldset>
)

export default MethodSectionset
