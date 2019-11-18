import React, { useState, useEffect } from 'react'

import api from '../api'
import MethodSection from './MethodSection'

const MethodSectionset = ({ psimi_id, ...props }) => {
    const [method, setMethod] = useState(null)

    useEffect(() => {
        psimi_id == null
            ? setMethod(null)
            : api.methods.select(psimi_id).then(method => setMethod(method))
    }, [psimi_id])

    return (
        <fieldset>
            <legend>
                <span className="fas fa-circle small text-info"></span>
                &nbsp;
                Method
            </legend>
            <MethodSection {...props} method={method} />
        </fieldset>
    )
}

export default MethodSectionset
