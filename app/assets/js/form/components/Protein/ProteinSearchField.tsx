import React from 'react'

import { ProteinType } from '../../src/types'

import { SearchField } from '../Shared/SearchField'

import { proteins as api } from '../../src/api'

type Props = {
    type: ProteinType,
    select: (accession: string) => void,
}

const placeholders: Record<ProteinType, string> = {
    'h': 'Search a human uniprot entry',
    'v': 'Search a viral uniprot entry',
}

const helps: Record<ProteinType, string> = {
    'h': 'You may use + to perform queries with multiple search terms (eg: bile acid + transport)',
    'v': 'You may use + to perform queries with multiple search terms (eg: influenza A + swine + thailand)',
}

export const ProteinSearchField: React.FC<Props> = ({ type, ...props }) => {
    return (
        <SearchField {...props}
            type={type == 'h' ? 'human' : 'virus'}
            search={(query: string) => api.search(type, query).read()}
            placeholder={placeholders[type]}
            help={helps[type]}
        />
    )
}
