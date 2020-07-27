import React from 'react'
import { useAction } from '../../src/hooks'

import { ProteinType, InteractorI } from '../../src/types'
import { selectProtein } from '../../src/reducer'
import { proteins as api } from '../../src/api'

import { SearchField } from '../Shared/SearchField'

type Props = {
    i: InteractorI,
    type: ProteinType,
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
            type={type == 'h' ? 'human' : 'virus'}
            select={(accession: string) => select({ i, accession })}
            search={(query: string) => api.search(type, query).read()}
            placeholder={placeholders[type]}
            help={helps[type]}
        />
    )
}
