import React, { useState } from 'react'

import MappingImg from './MappingImg'

const MappingList = ({ type, interactor }) => {
    const length = interactor.protein.sequence.length
    const start = interactor.start == '' ? 1 : interactor.start
    const stop = interactor.stop == '' ? length : interactor.stop

    return (
        <React.Fragment>
            <div className="row">
                <div className="col">
                    <MappingImg type={type} start={start} stop={stop} length={length} />
                </div>
            </div>
            {interactor.mapping.length == 0 ? (
                <p>
                    No mapping yet.
                </p>
            ) : (
                <div></div>
            )}
        </React.Fragment>
    )
}

export default MappingList;
