export type AppState = {
    run_id: number
    pmid: number
    type: DescriptionType
    description: Description
    interactorUI1: InteractorUI
    interactorUI2: InteractorUI
    saving: boolean
    feedback: Feedback | null
}

export type InteractorUI = {
    editing: boolean
    processing: boolean
    alignment: Alignment | null
}

export type AppProps = {
    type: DescriptionType
    stable_id: string
    method_id: number | null
    saving: boolean
    savable: boolean
    resetable: boolean
    feedback: Feedback | null
}

export type InteractorProps = {
    i: InteractorI
    type: ProteinType
    protein_id: number | null
    name: string
    start: number | null
    stop: number | null
    mapping: Alignment[]
} & InteractorUI

export type DescriptionType = 'hh' | 'vh'

export type Description = {
    stable_id: string
    method_id: number | null
    interactor1: Interactor
    interactor2: Interactor
}

export type InteractorI = 1 | 2

export type Interactor = {
    protein_id: number | null
    name: string
    start: number | null
    stop: number | null
    mapping: Alignment[]
}

export type Alignment = {
    sequence: string
    isoforms: Array<{
        accession: string
        occurrences: Array<{
            start: number
            stop: number
            identity: number
        }>
    }>,
}

export type Method = {
    id: number
    psimi_id: string
    name: string
}

export type ProteinType = 'h' | 'v'

export type Protein = {
    id: number
    type: ProteinType
    accession: string
    version: string
    current_version: string | null
    taxon: string
    name: string
    description: string
    sequence: string
    obsolete: boolean
    isoforms: Isoform[]
    chains: Chain[]
    domains: Domain[]
    matures: Mature[]
    sequences: Sequences
}

export type Isoform = {
    accession: string
    sequence: string
    is_canonical: boolean
}

export type Chain = {
    key: string
    description: string
    start: number
    stop: number
}

export type Domain = {
    type: string
    description: string
    start: number
    stop: number
}

export type ScaledDomain = {
    type: string
    description: string
    start: number
    stop: number
    valid: boolean
}

export type Mature = {
    name: string
    start: number
    stop: number
}

export type Sequences = Record<string, string>

export type Feedback = {
    success: boolean
    errors: string[]
}

export type SearchType = 'method' | 'human' | 'virus'

export type SearchResult = {
    id: number
    label: string
}

export type Resource<T> = {
    read: () => T
}
