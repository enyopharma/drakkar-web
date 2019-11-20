import React from 'react'

import { SearchResult, ProteinType, Protein } from '../types'

import { proteins as api } from '../api'
import { SearchField } from './SearchField'

type Props = {
    type: ProteinType,
    query: string,
    protein: Protein,
    editable: boolean,
    update: (query: string) => void,
    select: (protein: string) => void,
    unselect: () => void,
}

export const UniprotSection: React.FC<Props> = ({ type, query, protein, editable, update, select, unselect }) => {
    const search = (q: string): Promise<SearchResult[]> => api.search(type, q).then(proteins => {
        return proteins.map(protein => ({
            value: protein.accession, label: [
                protein.accession,
                protein.taxon,
                protein.name,
                protein.description,
            ].join(' - '),
        }))
    })

    return (
        <div className="row">
            <div className="col">
                <div style={{ display: protein == null ? 'block' : 'none' }}>
                    <SearchField
                        query={query}
                        update={update}
                        search={search}
                        select={select}
                        placeholder="Search an uniprot entry..."
                    />
                </div>
                {protein == null ? null : (
                    <div className={'mb-0 alert alert-' + (type == 'h' ? 'primary' : 'danger')}>
                        <strong>{protein.accession}</strong> - {[
                            protein.taxon,
                            protein.name,
                            protein.description,
                        ].join(' - ')}
                        <button
                            type="button"
                            className="close"
                            onClick={e => unselect()}
                            disabled={!editable}
                        >
                            <span>&times;</span>
                        </button>
                    </div>
                )}
            </div>
        </div>
    )
}
