import React from 'react'

const ResetFormGroup = ({ sequence, update }) => (
    <div className="form-group row">
        <div className="col-3 offset-9">
            <button
                type="button"
                className="btn btn-block btn-info"
                onClick={e => update(1, sequence.length)}
            >
                Reset to full length
            </button>
        </div>
    </div>
)

export default ResetFormGroup
