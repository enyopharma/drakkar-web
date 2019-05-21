import React from 'react'

import MethodField from './MethodField'

const MethodFieldset = ({ method, actions }) => (
    <fieldset>
        <legend>
            <i className="fas fa-circle small text-info" />
            &nbsp;
            Method
        </legend>
        <div className="row">
            <div className="col">
                <MethodField
                    method={method}
                    select={actions.selectMethod}
                    unselect={actions.unselectMethod}
                />
            </div>
        </div>
    </fieldset>
)

export default MethodFieldset
