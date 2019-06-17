import React, { useState } from 'react'

import Alignment from './Alignment'

const MappingDisplay = ({ type, width, mapping, remove }) => {
    return (
        <React.Fragment>
            {mapping.map((alignment, i) => (
                <div key={i} className="row">
                    <div className="col">
                        <Alignment
                            key={i}
                            type={type}
                            width={width}
                            alignment={alignment}
                            remove={() => remove(i)}
                        />
                    </div>
                </div>
            ))}
        </React.Fragment>
    )
}

export default MappingDisplay;
