import React from 'react'

import SearchInput from './SearchInput'
import SelectedValue from './SelectedValue'

const format = protein => [
    protein.accession,
    protein.name,
    protein.description
].join(' - ');

const ProteinSearchInput = ({ type, protein, search, select, unselect }) => (protein == null ?
    <SearchInput
        search={(q, update) => search(type, q, update)}
        select={select}
        format={format}
    >
        {type == 'h' ? 'Serach a human protein...' : 'Serach a viral protein...'}
    </SearchInput>
    :
    <SelectedValue color={type == 'h' ? 'primary' : 'danger'} unselect={unselect}>
        <strong>{protein.accession}</strong> - {[
            protein.name,
            protein.description,
        ].join(' - ')}
    </SelectedValue>
)

export default ProteinSearchInput
