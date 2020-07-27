import React from 'react'
import { useAction } from '../../src/hooks'
import { proteins as api } from '../../src/api'
import { selectProtein } from '../../src/reducer'
import { ProteinType, InteractorI, SearchType } from '../../src/types'

import { SearchField } from '../Shared/SearchField'

type Props = {
    i: InteractorI,
    type: ProteinType,
}

const types: Record<ProteinType, SearchType> = {
    'h': 'human',
    'v': 'virus',
}

const placeholders: Record<ProteinType, string> = {
    'h': 'Search a human uniprot entry',
    'v': 'Search a viral uniprot entry',
}

const helps: Record<ProteinType, string> = {
    'h': 'You may use + to perform queries with multiple search terms (eg: bile acid + transport)',
    'v': 'You may use + to perform queries with multiple search terms (eg: influenza A + swine + thailand)',
}

export const ProteinSearchField: React.FC<Props> = ({ i, type }) => {
    const select = useAction(selectProtein)

    return (
        <SearchField
            type={types[type]}
            select={(accession: string) => select({ i, accession })}
            search={(query: string) => api.search(type, query).read()}
            placeholder={placeholders[type]}
            help={helps[type]}
        />
    )
}
