import React, { useState } from 'react'

import Alignment from './Alignment'

const MappingDisplay = ({ protein, remove }) => {
    return (
        <React.Fragment>
            {protein.mapping.map((alignment, i) => (
                <div key={i} className="row">
                    <div className="col">
                        <Alignment
                            key={i}
                            type={protein.type}
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
