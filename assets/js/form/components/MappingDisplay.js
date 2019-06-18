import React from 'react'

import Alignment from './Alignment'

const MappingDisplay = ({ type, mapping, remove }) => {
    return (
        <React.Fragment>
            {mapping.map((alignment, i) => (
                <div key={i} className="row">
                    <div className="col">
                        <Alignment
                            key={i}
                            type={type}
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
