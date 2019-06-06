import React from 'react'

import MethodSection from './MethodSection'

const MethodSectionset = (props) => {
    return (
        <fieldset>
            <legend>
                <i className="fas fa-circle small text-info" />
                &nbsp;
                Method
            </legend>
            <MethodSection { ...props } />
        </fieldset>
    )
}

export default MethodSectionset
