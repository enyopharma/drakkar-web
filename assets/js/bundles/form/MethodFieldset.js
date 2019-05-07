import React from 'react'

import MethodSearchInput from './MethodSearchInput'

const MethodFieldset = ({ method, actions }) => (
    <fieldset>
        <legend>
            <i className="fas fa-circle small text-info"></i>
            &nbsp;
            Method
        </legend>
        <div className="form-group row">
            <div className="col">
                <MethodSearchInput
                    method={method}
                    search={actions.searchMethod}
                    select={actions.selectMethod}
                    unselect={actions.unselectMethod}
                />
            </div>
        </div>
    </fieldset>
)

export default MethodFieldset
