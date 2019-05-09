import React from 'react'

import MappingImg from './MappingImg'

const MappingSection = ({ type, interactor }) => {
    const length = interactor.protein.sequence.length
    const start = interactor.start == '' ? 1 : interactor.start
    const stop = interactor.stop == '' ? length : interactor.stop

    return (
        <React.Fragment>
            <h4>Mapping</h4>
            <MappingImg type={type} start={start} stop={stop} length={length} />
        </React.Fragment>
    )
}

export default MappingSection;
