import React from 'react'

import UniprotField from './UniprotField'

const UniprotSection = ({ type, protein, processing, actions }) => (
    <React.Fragment>
        <h4>Uniprot entry</h4>
        <div className="row">
            <div className="col">
                <UniprotField
                    type={type}
                    protein={protein}
                    processing={processing}
                    select={actions.selectProtein}
                    unselect={actions.unselectProtein}
                />
            </div>
        </div>
    </React.Fragment>
)

export default UniprotSection
