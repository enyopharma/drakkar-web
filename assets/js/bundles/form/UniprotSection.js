import React from 'react'

import ProteinSearchInput from './ProteinSearchInput'

const UniprotSection = ({ type, protein, actions }) => (
    <React.Fragment>
        <h4>Uniprot entry</h4>
        <div className="form-group row">
            <div className="col">
                <ProteinSearchInput
                    type={type}
                    protein={protein}
                    search={actions.searchProtein}
                    select={actions.selectProtein}
                    unselect={actions.unselectProtein}
                />
            </div>
        </div>
    </React.Fragment>
)

export default UniprotSection
