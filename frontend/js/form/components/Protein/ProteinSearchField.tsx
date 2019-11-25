import React from 'react'

import { proteins as api } from '../../api'
import { ProteinType } from '../../src/types'

import { SearchField } from '../Shared/SearchField'

type Props = {
    type: ProteinType,
    query: string,
    update: (query: string) => void,
    select: (accession: string) => void,
}

const placeholders: Record<ProteinType, string> = {
    'h': 'Search a human uniprot entry',
    'v': 'Search a viral uniprot entry',
}

export const ProteinSearchField: React.FC<Props> = ({ type, ...props }) => {
    return (
        <SearchField {...props}
            type={type == 'h' ? 'human' : 'virus'}
            search={api.search(type, 5)}
            placeholder={placeholders[type]}
        />
    )
}
