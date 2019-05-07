import React from 'react'

const SelectedValue = ({ color, unselect, children }) => (
    <div className={'mb-0 alert alert-' + color}>
        {children}
        <button type="button" className="close" onClick={unselect}>
            <span>&times;</span>
        </button>
    </div>
);

export default SelectedValue
